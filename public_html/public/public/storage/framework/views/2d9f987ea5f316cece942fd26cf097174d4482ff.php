<?php if(count($event_related) > 0): ?>
    <div class="bravo-list-event-related mb-8 ">
        <div class="w-md-80 w-lg-50 text-center mx-md-auto mt-3">
            <h2 class="section-title text-black font-size-30 font-weight-bold mb-0"><?php echo e(__("You might also like...")); ?></h2>
        </div>
        <div class="row mt-5">
            <?php $__currentLoopData = $event_related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-3">
                    <?php echo $__env->make('Event::frontend.layouts.search.loop-grid',['row'=>$item,'include_param'=>0], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/earntbpk/public_html/themes/Mytravel/Event/Views/frontend/layouts/details/related.blade.php ENDPATH**/ ?>