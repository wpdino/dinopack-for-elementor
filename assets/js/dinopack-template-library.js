/**
 * DinoPack Elementor Template Library – adds button in editor and opens modal.
 */
(function ( $ ) {
	'use strict';

	var DinoPackLib = window.dinopackTemplateLibrary = window.dinopackTemplateLibrary || {};
	var insertIndex = -1;
	var cachedTemplates = null;

	var elementor_add_section_tmpl = $( '#tmpl-elementor-add-section' );
	if ( elementor_add_section_tmpl.length === 0 || typeof elementor === 'undefined' ) {
		return;
	}

	var iconUrl = ( typeof dinopackTemplateLibraryData !== 'undefined' && dinopackTemplateLibraryData.icon_url )
		? dinopackTemplateLibraryData.icon_url
		: '';
	var btnContent = iconUrl
		? '<img src="' + iconUrl + '" class="dinopack-button-icon" alt="DinoPack" width="14" height="14">'
		: '<span class="dinopack-button-letter">D</span>';
	var text = elementor_add_section_tmpl.text();
	text = text.replace(
		'<div class="elementor-add-section-drag-title',
		'<div class="elementor-add-section-area-button elementor-add-dinopack-templates-button" title="DinoPack Library">' + btnContent + '</div><div class="elementor-add-section-drag-title'
	);
	elementor_add_section_tmpl.text( text );

	elementor.on( 'preview:loaded', function () {
		$( elementor.$previewContents[0].body ).on( 'click', '.elementor-add-dinopack-templates-button', openLibrary );
	} );

	function showLoadingView() {
		$( '#dinopack-elementor-template-library-modal .dialog-lightbox-loading' ).show();
		$( '#dinopack-elementor-template-library-modal .dialog-lightbox-content' ).hide();
	}
	function hideLoadingView() {
		$( '#dinopack-elementor-template-library-modal .dialog-lightbox-content' ).show();
		$( '#dinopack-elementor-template-library-modal .dialog-lightbox-loading' ).hide();
	}

	function openLibrary() {
		insertIndex = $( this ).parents( '.elementor-section-wrap' ).length > 0
			? $( this ).parents( '.elementor-add-section' ).index()
			: -1;
		DinoPackLib.insertIndex = insertIndex;
		if ( ! elementorCommon ) { return; }

		if ( ! DinoPackLib.modal ) {
			DinoPackLib.modal = elementorCommon.dialogsManager.createWidget( 'lightbox', {
				id: 'dinopack-elementor-template-library-modal',
				className: 'elementor-templates-modal',
				message: '',
				hide: { auto: false, onClick: false, onOutsideClick: false, onOutsideContextMenu: false, onBackgroundClick: true },
				position: { my: 'center', at: 'center' },
				onShow: function () {
					var modal = DinoPackLib.modal;
					var header = modal.getElements( 'header' );
					var content = modal.getElements( 'content' );
					var loading = modal.getElements( 'loading' );
					var $modalEl = $( '#dinopack-elementor-template-library-modal' );

					if ( ! $modalEl.find( '.dinopack-modal-header' ).length ) {
						header.append( wp.template( 'dinopack-elementor-templates-modal__header' ) );
					}
					if ( ! $modalEl.find( '#dinopack-elementor-template-library-toolbar' ).length ) {
						content.append( wp.template( 'dinopack-elementor-template-library-tools' ) );
					}
					if ( ! $modalEl.find( '#dinopack_main_library_templates_panel' ).length ) {
						content.append( '<div id="dinopack_main_library_templates_panel" class="dinopack__main-view"></div>' );
					}
					var isDark = elementor.settings.editorPreferences.model.get( 'ui_theme' ) === 'dark';
					$modalEl.find( '#dinopack_main_library_templates_panel' ).toggleClass( 'dinopack-dark-mode', isDark );
					if ( ! $modalEl.find( '#dinopack-elementor-template-library-loading' ).length ) {
						loading.append( wp.template( 'dinopack-elementor-template-library-loading' ) );
					}

					$modalEl.find( '.dinopack-modal-close' ).off( 'click' ).on( 'click', function () { modal.hide(); } );
					$modalEl.find( '.dinopack-header-back-button' ).off( 'click' ).on( 'click', function () {
						$( this ).hide();
						$modalEl.find( '#dinopack-elementor-template-library-header-preview' ).hide();
						$modalEl.find( '#dinopack-elementor-template-library-toolbar' ).show();
						$modalEl.find( '.dinopack-modal-header__logo' ).show();
						dinopack_get_library_view();
					} );

					if ( ! DinoPackLib.filtersInitialized ) {
						$modalEl.find( '#dinopack-elementor-template-library-filter-theme, #dinopack-elementor-template-library-filter-category' ).on( 'change', function () {
							var themeVal = $modalEl.find( '#dinopack-elementor-template-library-filter-theme' ).val();
							var catVal = $modalEl.find( '#dinopack-elementor-template-library-filter-category' ).val();
							$modalEl.find( '.dinopack-item, h2.dinopack-templates-library-template-category' ).each( function () {
								var $el = $( this );
								var show = true;
								if ( themeVal && $el.data( 'theme' ) && $el.data( 'theme' ).indexOf( themeVal ) === -1 ) { show = false; }
								if ( catVal && $el.data( 'category' ) && $el.data( 'category' ).indexOf( catVal ) === -1 ) { show = false; }
								$el.toggle( show );
							} );
						} );
						DinoPackLib.filtersInitialized = true;
					}
					setTimeout( function () { dinopack_get_library_view(); }, 100 );
				}
			} );
			DinoPackLib.modal.getElements( 'message' ).append(
				DinoPackLib.modal.addElement( 'content' ),
				DinoPackLib.modal.addElement( 'loading' )
			);
		}
		DinoPackLib.modal.show();
	}

	function dinopack_get_library_view() {
		var $modalEl = $( '#dinopack-elementor-template-library-modal' );
		$modalEl.find( '.dinopack-modal-header__logo' ).show();
		$modalEl.find( '#dinopack-elementor-template-library-toolbar' ).show();
		$modalEl.find( '.dinopack-header-back-button' ).hide();
		$modalEl.find( '#dinopack-elementor-template-library-header-preview' ).hide();
		showLoadingView();
		if ( cachedTemplates === null ) {
			$.post( ajaxurl, { action: 'dinopack_get_templates_library_view' }, function ( data ) {
				hideLoadingView();
				cachedTemplates = data;
				$modalEl.find( '#dinopack_main_library_templates_panel' ).html( data );
				dinopack_update_actions();
			} ).fail( function () {
				hideLoadingView();
				$modalEl.find( '#dinopack_main_library_templates_panel' ).html( '<div class="dinopack-no-results">Could not load templates.</div>' );
			} );
		} else {
			hideLoadingView();
			$modalEl.find( '#dinopack_main_library_templates_panel' ).html( cachedTemplates );
			dinopack_update_actions();
		}
	}

	function dinopack_update_actions() {
		var $modalEl = $( '#dinopack-elementor-template-library-modal' );
		$modalEl.find( '.dinopack-btn-template-insert' ).off( 'click' ).on( 'click', function () {
			var filename = $( this ).attr( 'data-template-name' ) + '.json';
			showLoadingView();
			$.post( ajaxurl, { action: 'dinopack_get_content_from_export_file', filename: filename } )
				.done( function ( data ) {
					var content = data;
					if ( typeof data === 'object' && data !== null && data.success === false ) {
						elementor.templates.showErrorDialog( data.data && data.data.message ? data.data.message : 'Could not import template.' );
						hideLoadingView();
						return;
					}
					if ( typeof data === 'string' ) {
						try { content = JSON.parse( data ); } catch ( e ) {
							elementor.templates.showErrorDialog( 'Invalid template data.' );
							hideLoadingView();
							return;
						}
					}
					if ( typeof content === 'object' && content !== null && content.success !== false ) {
						if ( insertIndex === -1 ) {
							elementor.getPreviewView().addChildModel( content, { silent: 0 } );
						} else {
							elementor.getPreviewView().addChildModel( content, { at: insertIndex, silent: 0 } );
						}
						elementor.channels.data.trigger( 'template:after:insert', {} );
						if ( typeof $e !== 'undefined' && $e.internal ) {
							$e.internal( 'document/save/set-is-modified', { status: true } );
						} else {
							elementor.saver.setFlagEditorChange( true );
						}
						DinoPackLib.modal.hide();
					}
					hideLoadingView();
				} )
				.fail( function ( xhr ) {
					var msg = ( xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message ) ? xhr.responseJSON.data.message : 'The template could not be imported.';
					elementor.templates.showErrorDialog( msg );
					hideLoadingView();
				} );
		} );
		$modalEl.find( '.dinopack-template-thumb' ).off( 'click' ).on( 'click', function () {
			var data = JSON.parse( $( this ).attr( 'data-template' ) );
			var rawId = data.id || '';
			var slug = String( rawId ).toLowerCase().replace( /\s+/g, '-' ).replace( /[^a-z0-9_-]/g, '' );
			$modalEl.find( '.dinopack-modal-header__logo' ).hide();
			$modalEl.find( '#dinopack-elementor-template-library-toolbar' ).hide();
			$modalEl.find( '#dinopack-elementor-template-library-header-preview' ).show();
			$modalEl.find( '#dinopack-elementor-template-library-header-preview .dinopack-btn-template-insert' ).attr( 'data-template-name', slug );
			$modalEl.find( '.dinopack-header-back-button' ).show();
			showLoadingView();
			$.post( ajaxurl, { action: 'dinopack_get_preview', data: data }, function ( html ) {
				hideLoadingView();
				$modalEl.find( '#dinopack_main_library_templates_panel' ).html( html );
				dinopack_update_actions();
			} );
		} );
	}
})( jQuery );
