

<?php $__env->startSection('content'); ?>
    <form action="<?php echo e(route('hotel.admin.store',['id'=>($row->id) ? $row->id : '-1','lang'=>request()->query('lang')])); ?>" method="post">
        <?php echo csrf_field(); ?>
        <div class="container-fluid">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar"><?php echo e($row->id ? __('Edit: ').$row->title : __('Add new hotel')); ?></h1>
                    <?php if($row->slug): ?>
                        <p class="item-url-demo"><?php echo e(__("Permalink")); ?>: <?php echo e(url( config('hotel.hotel_route_prefix') )); ?>/<a href="#" class="open-edit-input" data-name="slug"><?php echo e($row->slug); ?></a>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="">
                    <?php if($row->id): ?>
                        <a class="btn btn-warning btn-xs" href="<?php echo e(route('hotel.admin.room.index',['hotel_id'=>$row->id])); ?>" target="_blank"><i class="fa fa-hand-o-right"></i> <?php echo e(__("Manage Rooms")); ?></a>
                    <?php endif; ?>
                    <?php if($row->slug): ?>
                        <a class="btn btn-primary btn-xs" href="<?php echo e($row->getDetailUrl(request()->query('lang'))); ?>" target="_blank"><?php echo e(__("View Hotel")); ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php if($row->id): ?>
                <?php echo $__env->make('Language::admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
            <div class="lang-content-box">
                <div class="row">
                    <div class="col-md-9">
                        <?php echo $__env->make('Hotel::admin.hotel.content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php echo $__env->make('Hotel::admin.hotel.pricing', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php echo $__env->make('Hotel::admin.hotel.location', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php echo $__env->make('Hotel::admin.hotel.surrounding', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php echo $__env->make('Core::admin/seo-meta/seo-meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <div class="col-md-3">
                        <div class="panel">
                            <div class="panel-title"><strong><?php echo e(__('Publish')); ?></strong></div>
                            <div class="panel-body">
                                <?php if(is_default_lang()): ?>
                                    <div>
                                        <label><input <?php if($row->status=='publish'): ?> checked <?php endif; ?> type="radio" name="status" value="publish"> <?php echo e(__("Publish")); ?>

                                        </label></div>
                                    <div>
                                        <label><input <?php if($row->status=='draft'): ?> checked <?php endif; ?> type="radio" name="status" value="draft"> <?php echo e(__("Draft")); ?>

                                        </label></div>
                                <?php endif; ?>
                                <div class="text-right">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> <?php echo e(__('Save Changes')); ?></button>
                                </div>
                            </div>
                        </div>
                        <?php if(is_default_lang()): ?>
                        <div class="panel">
                            <div class="panel-title"><strong><?php echo e(__("Author Setting")); ?></strong></div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <?php $user = !empty($row->author_id) ? App\User::find($row->author_id) : false;
                                    \App\Helpers\AdminForm::select2('author_id', [
                                        'configs' => [
                                            'ajax'        => [
                                                'url' => route('user.admin.getForSelect2'),
                                                'dataType' => 'json'
                                            ],
                                            'allowClear'  => true,
                                            'placeholder' => __('-- Select User --')
                                        ]
                                    ], !empty($user->id) ? [
                                        $user->id,
                                        $user->getDisplayName() . ' (#' . $user->id . ')'
                                    ] : false)
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if(is_default_lang()): ?>
                            <div class="panel">
                                <div class="panel-title"><strong><?php echo e(__("Availability")); ?></strong></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label><?php echo e(__('Hotel Featured')); ?></label>
                                        <br>
                                        <label>
                                            <input type="checkbox" name="is_featured" <?php if($row->is_featured): ?> checked <?php endif; ?> value="1"> <?php echo e(__("Enable featured")); ?>

                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php echo $__env->make('Hotel::admin.hotel.attributes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                            <div class="panel">
                                <div class="panel-title"><strong><?php echo e(__('Feature Image')); ?></strong></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <?php echo \Modules\Media\Helpers\FileHelper::fieldUpload('image_id',$row->image_id); ?>

                                    </div>
                                </div>
                            </div>


                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
    <?php echo App\Helpers\MapEngine::scripts(); ?>

    <script>
        jQuery(function ($) {
            new BravoMapEngine('map_content', {
                disableScripts: true,
                fitBounds: true,
                center: [<?php echo e($row->map_lat ?? setting_item('map_lat_default',51.505 )); ?>, <?php echo e($row->map_lng ?? setting_item('map_lng_default',-0.09 )); ?>],
                zoom:<?php echo e($row->map_zoom ?? "8"); ?>,
                ready: function (engineMap) {
                    <?php if($row->map_lat && $row->map_lng): ?>
                    engineMap.addMarker([<?php echo e($row->map_lat); ?>, <?php echo e($row->map_lng); ?>], {
                        icon_options: {}
                    });
                    <?php endif; ?>
                    engineMap.on('click', function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {
                            icon_options: {}
                        });
                        $("input[name=map_lat]").attr("value", dataLatLng[0]);
                        $("input[name=map_lng]").attr("value", dataLatLng[1]);
                    });
                    engineMap.on('zoom_changed', function (zoom) {
                        $("input[name=map_zoom]").attr("value", zoom);
                    });
                    if(bookingCore.map_provider === "gmap"){
                        engineMap.searchBox($('#customPlaceAddress'),function (dataLatLng) {
                            engineMap.clearMarkers();
                            engineMap.addMarker(dataLatLng, {
                                icon_options: {}
                            });
                            $("input[name=map_lat]").attr("value", dataLatLng[0]);
                            $("input[name=map_lng]").attr("value", dataLatLng[1]);
                        });
                    }
                    engineMap.searchBox($('.bravo_searchbox'),function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {
                            icon_options: {}
                        });
                        $("input[name=map_lat]").attr("value", dataLatLng[0]);
                        $("input[name=map_lng]").attr("value", dataLatLng[1]);
                    });
                }
            });
        })
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/earntbpk/public_html/modules/Hotel/Views/admin/detail.blade.php ENDPATH**/ ?>