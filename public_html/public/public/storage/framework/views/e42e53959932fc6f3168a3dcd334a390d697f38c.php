<tr>
    <td class="booking-history-type">
        <?php if($service = $booking->service): ?>
            <i class="<?php echo e($service->getServiceIconFeatured()); ?>"></i>
        <?php endif; ?>
        <small><?php echo e($booking->object_model); ?></small>
    </td>
    <td>
        <?php if($service = $booking->service): ?>
            <?php
                $translation = $service->translate();
            ?>
            <a target="_blank" href="<?php echo e($service->getDetailUrl()); ?>">
                <?php echo e($translation->title); ?>

            </a>
        <?php else: ?>
            <?php echo e(__("[Deleted]")); ?>

        <?php endif; ?>
    </td>
    <td class="a-hidden"><?php echo e(display_date($booking->created_at)); ?></td>
    <td class="a-hidden">
        <?php echo e(__("Start date")); ?> : <?php echo e(display_date($booking->start_date)); ?> <br>
        <?php echo e(__("End date")); ?> : <?php echo e(display_date($booking->end_date)); ?> <br>
        <?php echo e(__("Duration")); ?> :

        <?php if($booking->duration_nights <= 1): ?>
            <?php echo e(__(':count night',['count'=>$booking->duration_nights])); ?>

        <?php else: ?>
            <?php echo e(__(':count nights',['count'=>$booking->duration_nights])); ?>

        <?php endif; ?>
    </td>
    <td><?php echo e(format_money_main($booking->total)); ?></td>
    <td><?php echo e(format_money($booking->paid)); ?></td>
    <td><?php echo e(format_money($booking->total - $booking->paid)); ?></td>
    <td class="<?php echo e($booking->status); ?> a-hidden"><?php echo e($booking->statusName); ?></td>
    <td width="2%">
        <?php if($service = $booking->service): ?>
            <a class="btn btn-xs btn-primary btn-info-booking" data-toggle="modal" data-target="#modal-booking-<?php echo e($booking->id); ?>">
                <i class="fa fa-info-circle"></i><?php echo e(__("Details")); ?>

            </a>
            <?php echo $__env->make($service->checkout_booking_detail_modal_file ?? '', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
        <a href="<?php echo e(route('user.booking.invoice',['code'=>$booking->code])); ?>" class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1" onclick="window.open(this.href); return false;">
            <i class="fa fa-print"></i><?php echo e(__("Invoice")); ?>

        </a>
    </td>
</tr>
<?php /**PATH /home/earntbpk/public_html/themes/Mytravel/Hotel/Views/frontend/bookingHistory/loop.blade.php ENDPATH**/ ?>