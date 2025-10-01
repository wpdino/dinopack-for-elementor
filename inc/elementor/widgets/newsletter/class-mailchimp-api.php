<?php
/**
 * MailChimp API v3.0 Wrapper for WordPress
 * 
 * A modern, WordPress-friendly wrapper for MailChimp API v3.0
 * Designed specifically for the DinoPack Newsletter Widget
 * 
 * @package DinoPack
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DinoPack_MailChimp_API {
    
    /**
     * MailChimp API endpoint base URL
     */
    private $api_endpoint = 'https://<dc>.api.mailchimp.com/3.0';
    
    /**
     * API key
     */
    private $api_key;
    
    /**
     * Data center extracted from API key
     */
    private $data_center;
    
    /**
     * Request timeout in seconds
     */
    private $timeout = 30;
    
    /**
     * Last request response
     */
    private $last_response = null;
    
    /**
     * Last request error
     */
    private $last_error = null;
    
    /**
     * Constructor
     * 
     * @param string $api_key MailChimp API key
     * @throws Exception If API key is invalid
     */
    public function __construct($api_key) {
        if (empty($api_key)) {
            throw new Exception('MailChimp API key is required');
        }
        
        if (strpos($api_key, '-') === false) {
            throw new Exception('Invalid MailChimp API key format');
        }
        
        $this->api_key = $api_key;
        list(, $this->data_center) = explode('-', $api_key);
        $this->api_endpoint = str_replace('<dc>', $this->data_center, $this->api_endpoint);
    }
    
    /**
     * Subscribe email to a MailChimp list
     * 
     * @param string $list_id MailChimp list ID
     * @param string $email Email address to subscribe
     * @param array $merge_fields Additional merge fields (optional)
     * @param array $tags Tags to add to subscriber (optional)
     * @return bool|array Success status or response data
     */
    public function subscribe($list_id, $email, $merge_fields = array(), $tags = array()) {
        if (empty($list_id) || empty($email)) {
            $this->last_error = 'List ID and email are required';
            return false;
        }
        
        if (!is_email($email)) {
            $this->last_error = 'Invalid email address';
            return false;
        }
        
        $data = array(
            'email_address' => sanitize_email($email),
            'status' => 'subscribed',
        );
        
        // Only add merge_fields if not empty
        $sanitized_merge_fields = $this->sanitize_merge_fields($merge_fields);
        if (!empty($sanitized_merge_fields)) {
            $data['merge_fields'] = $sanitized_merge_fields;
        }
        
        if (!empty($tags)) {
            $data['tags'] = array_map('sanitize_text_field', $tags);
        }
        
        $response = $this->make_request('POST', "lists/{$list_id}/members", $data);
        
        if (is_wp_error($response)) {
            $this->last_error = $response->get_error_message();
            return false;
        }
        
        $this->last_response = $response;
        
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code >= 200 && $status_code < 300) {
            return json_decode(wp_remote_retrieve_body($response), true);
        }
        
        $this->last_error = $this->get_error_message($response);
        return false;
    }
    
    /**
     * Update subscriber information
     * 
     * @param string $list_id MailChimp list ID
     * @param string $email Email address
     * @param array $merge_fields Additional merge fields
     * @return bool|array Success status or response data
     */
    public function update_subscriber($list_id, $email, $merge_fields = array()) {
        if (empty($list_id) || empty($email)) {
            $this->last_error = 'List ID and email are required';
            return false;
        }
        
        $subscriber_hash = $this->get_subscriber_hash($email);
        
        $data = array();
        
        // Only add merge_fields if not empty
        $sanitized_merge_fields = $this->sanitize_merge_fields($merge_fields);
        if (!empty($sanitized_merge_fields)) {
            $data['merge_fields'] = $sanitized_merge_fields;
        }
        
        $response = $this->make_request('PATCH', "lists/{$list_id}/members/{$subscriber_hash}", $data);
        
        if (is_wp_error($response)) {
            $this->last_error = $response->get_error_message();
            return false;
        }
        
        $this->last_response = $response;
        
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code >= 200 && $status_code < 300) {
            return json_decode(wp_remote_retrieve_body($response), true);
        }
        
        $this->last_error = $this->get_error_message($response);
        return false;
    }
    
    /**
     * Unsubscribe email from a MailChimp list
     * 
     * @param string $list_id MailChimp list ID
     * @param string $email Email address to unsubscribe
     * @return bool Success status
     */
    public function unsubscribe($list_id, $email) {
        if (empty($list_id) || empty($email)) {
            $this->last_error = 'List ID and email are required';
            return false;
        }
        
        $subscriber_hash = $this->get_subscriber_hash($email);
        
        $data = array(
            'status' => 'unsubscribed',
        );
        
        $response = $this->make_request('PATCH', "lists/{$list_id}/members/{$subscriber_hash}", $data);
        
        if (is_wp_error($response)) {
            $this->last_error = $response->get_error_message();
            return false;
        }
        
        $this->last_response = $response;
        
        if (wp_remote_retrieve_response_code($response) === 200) {
            return true;
        }
        
        $this->last_error = $this->get_error_message($response);
        return false;
    }
    
    /**
     * Get subscriber information
     * 
     * @param string $list_id MailChimp list ID
     * @param string $email Email address
     * @return bool|array Subscriber data or false on failure
     */
    public function get_subscriber($list_id, $email) {
        if (empty($list_id) || empty($email)) {
            $this->last_error = 'List ID and email are required';
            return false;
        }
        
        $subscriber_hash = $this->get_subscriber_hash($email);
        
        $response = $this->make_request('GET', "lists/{$list_id}/members/{$subscriber_hash}");
        
        if (is_wp_error($response)) {
            $this->last_error = $response->get_error_message();
            return false;
        }
        
        $this->last_response = $response;
        
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code >= 200 && $status_code < 300) {
            return json_decode(wp_remote_retrieve_body($response), true);
        }
        
        $this->last_error = $this->get_error_message($response);
        return false;
    }
    
    /**
     * Get all lists
     * 
     * @param int $count Number of lists to return (max 1000)
     * @return bool|array Lists data or false on failure
     */
    public function get_lists($count = 10) {
        $args = array(
            'count' => min($count, 1000),
        );
        
        $response = $this->make_request('GET', 'lists', $args);
        
        if (is_wp_error($response)) {
            $this->last_error = $response->get_error_message();
            return false;
        }
        
        $this->last_response = $response;
        
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code >= 200 && $status_code < 300) {
            return json_decode(wp_remote_retrieve_body($response), true);
        }
        
        $this->last_error = $this->get_error_message($response);
        return false;
    }
    
    /**
     * Test API connection
     * 
     * @return bool Connection status
     */
    public function test_connection() {
        $response = $this->make_request('GET', 'ping');
        
        if (is_wp_error($response)) {
            $this->last_error = $response->get_error_message();
            return false;
        }
        
        $this->last_response = $response;
        
        $status_code = wp_remote_retrieve_response_code($response);
        return ($status_code >= 200 && $status_code < 300);
    }
    
    /**
     * Get the last error message
     * 
     * @return string|null Error message
     */
    public function get_last_error() {
        return $this->last_error;
    }
    
    /**
     * Get the last response
     * 
     * @return array|null Response data
     */
    public function get_last_response() {
        return $this->last_response;
    }
    
    /**
     * Check if the last request was successful
     * 
     * @return bool Success status
     */
    public function is_success() {
        if (!$this->last_response) {
            return false;
        }
        
        $code = wp_remote_retrieve_response_code($this->last_response);
        return $code >= 200 && $code < 300;
    }
    
    /**
     * Make HTTP request to MailChimp API
     * 
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @return array|WP_Error Response or error
     */
    private function make_request($method, $endpoint, $data = array()) {
        $url = $this->api_endpoint . '/' . ltrim($endpoint, '/');
        
        $args = array(
            'method' => $method,
            'timeout' => $this->timeout,
            'headers' => array(
                'Authorization' => 'apikey ' . $this->api_key,
                'Content-Type' => 'application/json',
                'User-Agent' => 'DinoPack-Newsletter-Widget/1.0 (WordPress)',
            ),
            'sslverify' => true,
        );
        
        if (!empty($data)) {
            if ($method === 'GET') {
                $url .= '?' . http_build_query($data);
            } else {
                $args['body'] = json_encode($data);
            }
        }
        
        return wp_remote_request($url, $args);
    }
    
    /**
     * Generate subscriber hash for MailChimp API
     * 
     * @param string $email Email address
     * @return string MD5 hash
     */
    private function get_subscriber_hash($email) {
        return md5(strtolower($email));
    }
    
    /**
     * Sanitize merge fields
     * 
     * @param array $merge_fields Raw merge fields
     * @return array Sanitized merge fields
     */
    private function sanitize_merge_fields($merge_fields) {
        $sanitized = array();
        
        foreach ($merge_fields as $key => $value) {
            $sanitized[sanitize_key($key)] = sanitize_text_field($value);
        }
        
        return $sanitized;
    }
    
    /**
     * Extract error message from API response
     * 
     * @param array $response WordPress HTTP response
     * @return string Error message
     */
    private function get_error_message($response) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        // Build detailed error message
        $error_message = '';
        
        if (isset($data['title'])) {
            $error_message = $data['title'];
        }
        
        if (isset($data['detail'])) {
            $error_message .= ($error_message ? ': ' : '') . $data['detail'];
        }
        
        // Add field-specific errors if available
        if (isset($data['errors']) && is_array($data['errors'])) {
            $field_errors = array();
            foreach ($data['errors'] as $error) {
                if (isset($error['field']) && isset($error['message'])) {
                    $field_errors[] = $error['field'] . ': ' . $error['message'];
                }
            }
            if (!empty($field_errors)) {
                $error_message .= ' (' . implode(', ', $field_errors) . ')';
            }
        }
        
        if (empty($error_message)) {
            $code = wp_remote_retrieve_response_code($response);
            $error_message = "HTTP Error {$code}: " . wp_remote_retrieve_response_message($response);
        }
        
        return $error_message;
    }
}
