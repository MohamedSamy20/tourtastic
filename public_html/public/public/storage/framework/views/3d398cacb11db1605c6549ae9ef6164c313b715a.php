<?php
if(!$user->hasPermission('boat_create')) return;
?>
<?php if(!empty($services) and $services->total()): ?>
    <div class="bravo-profile-list-services">
        <?php echo $__env->make('Boat::frontend.blocks.list-boat.index', ['rows'=>$services,'style_list'=>'normal','desc'=>' ','title'=>!empty($view_all) ? __('Boat by :name',['name'=>$user->first_name]) :'','col'=>4], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="container">
            <?php if(!empty($view_all)): ?>
                <div class="review-pag-wrapper">
                    <div class="bravo-pagination">
                        <?php echo e($services->appends(request()->query())->links()); ?>

                    </div>
                    <div class="review-pag-text text-center">
                        <?php echo e(__("Showing :from - :to of :total total",["from"=>$services->firstItem(),"to"=>$services->lastItem(),"total"=>$services->total()])); ?>

                    </div>
                </div>
            <?php else: ?>
                <div class="text-center mt30"><a class="btn btn-sm btn-primary" href="<?php echo e(route('user.profile.services',['id'=>$user->user_name ?? $user->id,'type'=>'boat'])); ?>"><?php echo e(__('View all (:total)',['total'=>$services->total()])); ?></a></div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/earntbpk/public_html/themes/Mytravel/Boat/Views/frontend/profile/service.blade.php ENDPATH**/ ?>