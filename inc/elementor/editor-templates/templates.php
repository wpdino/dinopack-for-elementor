<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<script type="text/template" id="tmpl-dinopack-elementor-templates-modal__header">
	<div class="dinopack-modal-header">
		<div class="dinopack-modal-header__logo-area">
			<div class="dinopack-modal-header__logo">
				<span class="dinopack-modal-header__logo-icon">
					<img src="<?php echo esc_url( DINOPACK_URL . 'assets/images/dinopack-logo.svg' ); ?>" alt="DinoPack" class="dinopack-modal-logo-img">
				</span>
				<span class="dinopack-modal-header__title"><?php esc_html_e( 'DinoPack Library', 'dinopack-for-elementor' ); ?></span>
			</div>
			<div id="dinopack-elementor-template-library-header-preview-back" class="dinopack-header-back-button" style="display:none;">
				<i class="eicon-arrow-left" aria-hidden="true"></i>
				<span><?php esc_html_e( 'Back to Library', 'dinopack-for-elementor' ); ?></span>
			</div>
		</div>
		<div class="dinopack-modal-header__items-area">
			<div class="dinopack-modal-close" title="<?php esc_attr_e( 'Close', 'dinopack-for-elementor' ); ?>">
				<i class="eicon-close" aria-hidden="true"></i>
				<span class="elementor-screen-only"><?php esc_html_e( 'Close', 'dinopack-for-elementor' ); ?></span>
			</div>
			<div id="dinopack-elementor-template-library-header-preview" class="dinopack-modal-header__preview" style="display:none;">
				<div id="dinopack-elementor-template-library-header-preview-insert-wrapper" class="dinopack-modal-header__item">
					<a class="dinopack-btn-template-insert elementor-button" data-template-name="">
						<i class="eicon-file-download" aria-hidden="true"></i>
						<span class="elementor-button-title"><?php esc_html_e( 'Insert Template', 'dinopack-for-elementor' ); ?></span>
					</a>
				</div>
			</div>
		</div>
	</div>
</script>
<script type="text/template" id="tmpl-dinopack-elementor-template-library-loading">
	<div id="dinopack-elementor-template-library-loading" class="elementor-template-library-loading">
		<div class="elementor-loader-wrapper">
			<div class="elementor-loader">
				<div class="elementor-loader-boxes">
					<div class="elementor-loader-box"></div>
					<div class="elementor-loader-box"></div>
					<div class="elementor-loader-box"></div>
					<div class="elementor-loader-box"></div>
				</div>
			</div>
			<div class="elementor-loading-title"><?php esc_html_e( 'Loading', 'dinopack-for-elementor' ); ?></div>
		</div>
	</div>
</script>
<script type="text/template" id="tmpl-dinopack-elementor-template-library-tools">
	<div id="dinopack-elementor-template-library-toolbar">
		<div id="dinopack-elementor-template-library-filter-toolbar" class="elementor-template-library-filter-toolbar">
			<div id="dinopack-elementor-template-library-filter">
				<select id="dinopack-elementor-template-library-filter-theme" class="elementor-template-library-filter-select" name="theme" data-filter="theme">
					<option value=""><?php esc_html_e( 'All themes', 'dinopack-for-elementor' ); ?></option>
				</select>
				<select id="dinopack-elementor-template-library-filter-category" class="elementor-template-library-filter-select" name="category" data-filter="category">
					<option value=""><?php esc_html_e( 'All categories', 'dinopack-for-elementor' ); ?></option>
				</select>
			</div>
		</div>
	</div>
</script>
