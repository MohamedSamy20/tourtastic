<?php if($row->banner_image_id): ?>
    <div class="bravo_banner">
        <?php if(!empty($breadcrumbs)): ?>
            <div class="container">
                <nav class="py-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter mb-0 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="<?php echo e(url('')); ?>"><?php echo e(__('Home')); ?></a></li>
                        <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 <?php echo e($breadcrumb['class'] ?? ''); ?>">
                                <?php if(!empty($breadcrumb['url'])): ?>
                                    <a href="<?php echo e(url($breadcrumb['url'])); ?>"><?php echo e($breadcrumb['name']); ?></a>
                                <?php else: ?>
                                    <?php echo e($breadcrumb['name']); ?>

                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ol>
                </nav>
            </div>
        <?php endif; ?>
        <div class="mb-4 mb-lg-8">
            <img class="img-fluid" src="<?php echo e($row->getBannerImageUrlAttribute('full')); ?>" alt="<?php echo clean($translation->title); ?>">
            <div class="container">
                <div class="position-relative">
                    <div class="position-absolute video-gallery">
                        <div class="flex-horizontal-center">
                            <?php if($row->video): ?>
                                <a class="travel-fancybox btn btn-white transition-3d-hover py-2 px-md-4 px-3 shadow-6 mr-1" href="javascript:;" data-src="<?php echo e(handleVideoUrl($row->video)); ?>" data-speed="700">
                                    <i class="flaticon-movie mr-md-2 font-size-18 text-primary"></i><span class="d-none d-md-inline"><?php echo e(__("Video")); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php /**PATH /home/earntbpk/public_html/themes/Mytravel/Event/Views/frontend/layouts/details/banner.blade.php ENDPATH**/ ?>