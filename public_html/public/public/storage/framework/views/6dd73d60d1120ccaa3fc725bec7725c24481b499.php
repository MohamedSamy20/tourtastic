<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="<?php echo e($html_class ?? ''); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php ($favicon = setting_item('site_favicon')); ?>
    <link rel="icon" type="image/png" href="<?php echo e(!empty($favicon)?get_file_url($favicon,'full'):url('images/favicon.png')); ?>" />
    <?php echo $__env->make('Layout::parts.seo-meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <link href="<?php echo e(asset('libs/bootstrap/css/bootstrap.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('libs/font-awesome/css/font-awesome.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('libs/ionicons/css/ionicons.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('libs/icofont/icofont.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('dist/frontend/css/notification.css')); ?>" rel="newest stylesheet">
    <link href="<?php echo e(asset('dist/frontend/css/app.css?_ver='.config('app.asset_version'))); ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset("libs/daterange/daterangepicker.css")); ?>" >
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset("libs/select2/css/select2.min.css")); ?>" >
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel='stylesheet' id='google-font-css-css'  href='https://fonts.googleapis.com/css?family=Poppins%3A400%2C500%2C600' type='text/css' media='all' />
    <script>
        var bookingCore = {
            url:'<?php echo e(url( app_get_locale() )); ?>',
            url_root:'<?php echo e(url('')); ?>',
            admin_url:'<?php echo e(route('admin.index')); ?>',
            booking_decimals:<?php echo e((int)get_current_currency('currency_no_decimal',2)); ?>,
			thousand_separator:'<?php echo e(get_current_currency('currency_thousand')); ?>',
			decimal_separator:'<?php echo e(get_current_currency('currency_decimal')); ?>',
			currency_position:'<?php echo e(get_current_currency('currency_format')); ?>',
			currency_symbol:'<?php echo e(currency_symbol()); ?>',
			currency_rate:'<?php echo e(get_current_currency('rate',1)); ?>',
            date_format:'<?php echo e(get_moment_date_format()); ?>',
            map_provider:'<?php echo e(setting_item('map_provider')); ?>',
            map_gmap_key:'<?php echo e(setting_item('map_gmap_key')); ?>',
            map_options:{
                map_lat_default:'<?php echo e(setting_item('map_lat_default')); ?>',
                map_lng_default:'<?php echo e(setting_item('map_lng_default')); ?>',
                map_clustering:'<?php echo e(setting_item('map_clustering')); ?>',
                map_fit_bounds:'<?php echo e(setting_item('map_fit_bounds')); ?>',
            },
            routes:{
                login:'<?php echo e(route('login')); ?>',
                register:'<?php echo e(route('auth.register')); ?>',
            },
            currentUser: <?php echo e((int)Auth::id()); ?>,
            isAdmin : <?php echo e(is_admin() ? 1 : 0); ?>,
            rtl: <?php echo e(setting_item_with_lang('enable_rtl') ? "1" : "0"); ?>,
            markAsRead:'<?php echo e(route('core.notification.markAsRead')); ?>',
            markAllAsRead:'<?php echo e(route('core.notification.markAllAsRead')); ?>',
            loadNotify : '<?php echo e(route('core.notification.loadNotify')); ?>',
            pusher_api_key : '<?php echo e(setting_item("pusher_api_key")); ?>',
            pusher_cluster : '<?php echo e(setting_item("pusher_cluster")); ?>',
        };
        var i18n = {
            warning:"<?php echo e(__("Warning")); ?>",
            success:"<?php echo e(__("Success")); ?>",
            confirm_delete:"<?php echo e(__("Do you want to delete?")); ?>",
            confirm:"<?php echo e(__("Confirm")); ?>",
            cancel:"<?php echo e(__("Cancel")); ?>",
        };
        var daterangepickerLocale = {
            "applyLabel": "<?php echo e(__('Apply')); ?>",
            "cancelLabel": "<?php echo e(__('Cancel')); ?>",
            "fromLabel": "<?php echo e(__('From')); ?>",
            "toLabel": "<?php echo e(__('To')); ?>",
            "customRangeLabel": "<?php echo e(__('Custom')); ?>",
            "weekLabel": "<?php echo e(__('W')); ?>",
            "first_day_of_week": <?php echo e(setting_item("site_first_day_of_the_weekin_calendar","1")); ?>,
            "daysOfWeek": [
                "<?php echo e(__('Su')); ?>",
                "<?php echo e(__('Mo')); ?>",
                "<?php echo e(__('Tu')); ?>",
                "<?php echo e(__('We')); ?>",
                "<?php echo e(__('Th')); ?>",
                "<?php echo e(__('Fr')); ?>",
                "<?php echo e(__('Sa')); ?>"
            ],
            "monthNames": [
                "<?php echo e(__('January')); ?>",
                "<?php echo e(__('February')); ?>",
                "<?php echo e(__('March')); ?>",
                "<?php echo e(__('April')); ?>",
                "<?php echo e(__('May')); ?>",
                "<?php echo e(__('June')); ?>",
                "<?php echo e(__('July')); ?>",
                "<?php echo e(__('August')); ?>",
                "<?php echo e(__('September')); ?>",
                "<?php echo e(__('October')); ?>",
                "<?php echo e(__('November')); ?>",
                "<?php echo e(__('December')); ?>"
            ],
        };

        var image_editer = {
            language: '<?php echo e(app()->getLocale()); ?>',
            translations: {
                <?php echo e(app()->getLocale()); ?>: {
                'header.image_editor_title': '<?php echo e(__('Image Editor')); ?>',
                    'header.toggle_fullscreen': '<?php echo e(__('Toggle fullscreen')); ?>',
                    'header.close': '<?php echo e(__('Close')); ?>',
                    'header.close_modal': '<?php echo e(__('Close window')); ?>',
                    'toolbar.download': '<?php echo e(__('Save Change')); ?>',
                    'toolbar.save': '<?php echo e(__('Save')); ?>',
                    'toolbar.apply': '<?php echo e(__('Apply')); ?>',
                    'toolbar.saveAsNewImage': '<?php echo e(__('Save As New Image')); ?>',
                    'toolbar.cancel': '<?php echo e(__('Cancel')); ?>',
                    'toolbar.go_back': '<?php echo e(__('Go Back')); ?>',
                    'toolbar.adjust': '<?php echo e(__('Adjust')); ?>',
                    'toolbar.effects': '<?php echo e(__('Effects')); ?>',
                    'toolbar.filters': '<?php echo e(__('Filters')); ?>',
                    'toolbar.orientation': '<?php echo e(__('Orientation')); ?>',
                    'toolbar.crop': '<?php echo e(__('Crop')); ?>',
                    'toolbar.resize': '<?php echo e(__('Resize')); ?>',
                    'toolbar.watermark': '<?php echo e(__('Watermark')); ?>',
                    'toolbar.focus_point': '<?php echo e(__('Focus point')); ?>',
                    'toolbar.shapes': '<?php echo e(__('Shapes')); ?>',
                    'toolbar.image': '<?php echo e(__('Image')); ?>',
                    'toolbar.text': '<?php echo e(__('Text')); ?>',
                    'adjust.brightness': '<?php echo e(__('Brightness')); ?>',
                    'adjust.contrast': '<?php echo e(__('Contrast')); ?>',
                    'adjust.exposure': '<?php echo e(__('Exposure')); ?>',
                    'adjust.saturation': '<?php echo e(__('Saturation')); ?>',
                    'orientation.rotate_l': '<?php echo e(__('Rotate Left')); ?>',
                    'orientation.rotate_r': '<?php echo e(__('Rotate Right')); ?>',
                    'orientation.flip_h': '<?php echo e(__('Flip Horizontally')); ?>',
                    'orientation.flip_v': '<?php echo e(__('Flip Vertically')); ?>',
                    'pre_resize.title': '<?php echo e(__('Would you like to reduce resolution before editing the image?')); ?>',
                    'pre_resize.keep_original_resolution': '<?php echo e(__('Keep original resolution')); ?>',
                    'pre_resize.resize_n_continue': '<?php echo e(__('Resize & Continue')); ?>',
                    'footer.reset': '<?php echo e(__('Reset')); ?>',
                    'footer.undo': '<?php echo e(__('Undo')); ?>',
                    'footer.redo': '<?php echo e(__('Redo')); ?>',
                    'spinner.label': '<?php echo e(__('Processing...')); ?>',
                    'warning.too_big_resolution': '<?php echo e(__('The resolution of the image is too big for the web. It can cause problems with Image Editor performance.')); ?>',
                    'common.x': '<?php echo e(__('x')); ?>',
                    'common.y': '<?php echo e(__('y')); ?>',
                    'common.width': '<?php echo e(__('width')); ?>',
                    'common.height': '<?php echo e(__('height')); ?>',
                    'common.custom': '<?php echo e(__('custom')); ?>',
                    'common.original': '<?php echo e(__('original')); ?>',
                    'common.square': '<?php echo e(__('square')); ?>',
                    'common.opacity': '<?php echo e(__('Opacity')); ?>',
                    'common.apply_watermark': '<?php echo e(__('Apply watermark')); ?>',
                    'common.url': '<?php echo e(__('URL')); ?>',
                    'common.upload': '<?php echo e(__('Upload')); ?>',
                    'common.gallery': '<?php echo e(__('Gallery')); ?>',
                    'common.text': '<?php echo e(__('Text')); ?>',
            }
            }
        };
    </script>
    <link href="<?php echo e(asset('dist/frontend/module/user/css/user.css')); ?>" rel="stylesheet">
    <!-- Styles -->
    <?php echo $__env->yieldContent('head'); ?>
    <style type="text/css">
        .bravo_topbar, .bravo_header, .bravo_footer {
            display: none;
        }
        html, body, .bravo_wrap, .bravo_user_profile,
        .bravo_user_profile > .container-fluid > .row-eq-height > .col-md-3 {
            min-height: 100vh !important;
        }
    </style>
    
    <link href="<?php echo e(route('core.style.customCss')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('libs/carousel-2/owl.carousel.css')); ?>" rel="stylesheet">
    <?php if(setting_item_with_lang('enable_rtl')): ?>
        <link href="<?php echo e(asset('dist/frontend/css/rtl.css')); ?>" rel="stylesheet">
    <?php endif; ?>
</head>
<body class="user-page <?php echo e($body_class ?? ''); ?> <?php if(setting_item_with_lang('enable_rtl')): ?> is-rtl <?php endif; ?>">
    <?php if(!is_demo_mode()): ?>
        <?php echo setting_item('body_scripts'); ?>

    <?php endif; ?>
    <div class="bravo_wrap">
        <?php echo $__env->make('Layout::parts.topbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('Layout::parts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="bravo_user_profile">
            <div class="container-fluid">
                <div class="row row-eq-height">
                    <div class="col-md-3">
                        <?php echo $__env->make('User::frontend.layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <div class="col-md-9">
                        <div class="user-form-settings">
                            <?php echo $__env->make('Layout::parts.user-bc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <?php echo $__env->yieldContent('content'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $__env->make('Layout::parts.footer',['is_user_page'=>1], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <script src="<?php echo e(asset('libs/filerobot-image-editor/filerobot-image-editor.min.js?_ver='.config('app.asset_version'))); ?>"></script>
    <?php if(!is_demo_mode()): ?>
    <?php echo setting_item('footer_scripts'); ?>

    <?php endif; ?>
</body>
</html>
<?php /**PATH /home/earntbpk/public_html/modules/Layout/user.blade.php ENDPATH**/ ?>