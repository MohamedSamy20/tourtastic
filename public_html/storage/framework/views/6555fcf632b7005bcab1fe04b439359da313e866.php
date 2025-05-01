<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar"><?php echo e(__('Flight Settings')); ?></h1>
        </div>
        <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-title"><strong><?php echo e(__('Ticket Issuance Settings')); ?></strong></div>
                    <div class="panel-body">
                        <form action="<?php echo e(route('flight.admin.settings.update')); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="auto_issue_ticket" value="1" <?php echo e($settings->auto_issue_ticket ? 'checked' : ''); ?>> 
                                            <?php echo e(__('Auto-issue tickets after payment')); ?>

                                        </label>
                                        <div class="form-text text-muted">
                                            <?php echo e(__('If enabled, tickets will be automatically issued after payment. If disabled, bookings will be held and marked as "under review".')); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit"><?php echo e(__('Save Changes')); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/customer/www/tourtastic.net/public_html/resources/views/admin/flight/settings/index.blade.php ENDPATH**/ ?>