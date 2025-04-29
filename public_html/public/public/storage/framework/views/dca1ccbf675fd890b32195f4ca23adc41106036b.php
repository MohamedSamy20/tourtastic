<?php if(!empty($list_term)): ?>
    <div class="bravo-featured-box bg-gray-2">
        <div class="container space-1">
            <?php if(!empty($title)): ?>
            <div class="w-md-80 w-lg-50 text-center mx-md-auto mb-5 mt-lg-10">
                <h2 class="section-title text-black font-size-30 font-weight-bold mb-0"> <?php echo e($title); ?></h2>
            </div>
            <?php endif; ?>
            <div class="row mb-2">
                <?php $__currentLoopData = $list_term; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $image_url = get_file_url($item->image_id, 'full');
                        $page_search = Modules\Space\Models\Space::getLinkForPageSearch(false , [ 'terms[]' =>  $item->id] );
                    ?>
                    <div class="col-md-6 col-xl-3">
                        <div class="rounded-xs mb-5">
                            <a class="d-block" href="<?php echo e($page_search); ?>">
                                <div class="bg-img-hero gradient-overlay-half-bg-white transition-3d-hover shadow-hover-2 max-height-200" style="background-image: url(<?php echo e(asset('themes/mytravel/images/space-background.jpg')); ?>);">
                                    <div class="text-center py-5">
                                        <i class="<?php echo e($item->icon); ?> font-size-50 text-red-lighter-1"></i>
                                        <h6 class="font-size-17 font-weight-bold text-gray-3 mt-2"><?php echo e($item->name); ?></h6>
                                        <span class="font-size-14 font-weight-normal text-gray-1"><?php echo e(__(':num :text',['num' => count($item->space),'text' => (count($item->space) > 1) ? 'Listings' : 'Listing'])); ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/earntbpk/public_html/themes/Mytravel/Space/Views/frontend/blocks/term-featured-box/index.blade.php ENDPATH**/ ?>