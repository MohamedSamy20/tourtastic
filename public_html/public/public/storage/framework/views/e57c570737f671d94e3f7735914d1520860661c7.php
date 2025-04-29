<div class="row">
    <div class="col-lg-4 col-xl-3 col-md-12">
        <?php echo $__env->make('Hotel::frontend.layouts.search.filter-search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <div class="col-lg-8 col-xl-9 col-md-12">
        <div class="bravo-list-item">
            <div class="d-flex justify-content-between align-items-center mb-4 topbar-search">
                <h3 class="font-size-21 font-weight-bold mb-0 text-lh-1">
                    <?php if($rows->total() > 1): ?>
                        <?php echo e(__(":count hotels found",['count'=>$rows->total()])); ?>

                    <?php else: ?>
                        <?php echo e(__(":count hotel found",['count'=>$rows->total()])); ?>

                    <?php endif; ?>
                </h3>
                <div class="control">
                    <?php echo $__env->make('Hotel::frontend.layouts.search.orderby', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
            <div class="list-item">
                    <?php if($rows->total() > 0): ?>
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('Hotel::frontend.layouts.search.loop-list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                            <?php echo e(__("Hotel not found")); ?>

                    <?php endif; ?>
            </div>
            <?php if($rows->total() > 0): ?>
                <div class="text-center text-md-left font-size-14 mb-3 text-lh-1"><?php echo e(__("Showing :from - :to of :total Hotels",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()])); ?></div>
            <?php endif; ?>
            <?php echo e($rows->appends(request()->query())->links()); ?>

        </div>
    </div>
</div>
<?php /**PATH /home/earntbpk/public_html/themes/Mytravel/Hotel/Views/frontend/layouts/search/list-item.blade.php ENDPATH**/ ?>