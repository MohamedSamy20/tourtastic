<div class="bravo-list-item bravo-list-hotel <?php if(!$rows->count()): ?> not-found <?php endif; ?>">
    <?php if($rows->count()): ?>
        <div class="text-paginate">
            <h2 class="text">
                <?php if($rows->total() > 1): ?>
                    <?php echo e(__(":count hotels found",['count'=>$rows->total()])); ?>

                <?php else: ?>
                    <?php echo e(__(":count hotel found",['count'=>$rows->total()])); ?>

                <?php endif; ?>
            </h2>
            <span class="count-string"><?php echo e(__("Showing :from - :to of :total Hotels",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()])); ?></span>
        </div>
        <div class="list-item">
            <div class="row">
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-12 col-md-12">
                        <?php echo $__env->make('Hotel::frontend.layouts.search.loop-list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="bravo-pagination">
            <?php echo e($rows->appends(array_merge(request()->query(),['_ajax'=>1]))->links()); ?>

        </div>
    <?php else: ?>
        <div class="not-found-box">
            <h3 class="n-title"><?php echo e(__("We couldn't find any hotels.")); ?></h3>
            <p class="p-desc"><?php echo e(__("Try changing your filter criteria")); ?></p>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH /home/customer/www/tourtastic.net/public_html/themes/Mytravel/Hotel/Views/frontend/layouts/search-map/list-item.blade.php ENDPATH**/ ?>