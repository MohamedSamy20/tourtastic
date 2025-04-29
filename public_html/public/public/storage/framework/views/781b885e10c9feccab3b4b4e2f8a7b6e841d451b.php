<?php $lang_local = app()->getLocale() ?>
<?php
    $service_translation = $service->translate($lang_local);
?>
<div class="shadow-soft bg-white rounded-sm booking-review">
    <div class="pt-5 pb-3 px-4 border-bottom">
        <a href="#" class="d-block mb-3">
            <img class="img-fluid rounded-sm" src="<?php echo e($service->image_url); ?>" alt="<?php echo clean($service_translation->title); ?>">
        </a>
        <a href="<?php echo e($service->getDetailUrl()); ?>" class="text-dark font-weight-bold mb-2 d-block">
            <?php echo clean($service_translation->title); ?>

        </a>
        <?php if($service_translation->address): ?>
            <div class="mb-1 flex-horizontal-center text-gray-1">
                <i class="icon flaticon-pin-1 mr-2 font-size-15"></i> <?php echo e($service_translation->address); ?>

            </div>
        <?php endif; ?>
    </div>
    <div id="basicsAccordionBooking">
        <div class="card rounded-0 border-top-0 border-left-0 border-right-0">
            <div class="card-header card-collapse bg-transparent border-0">
                <h5 class="mb-0">
                    <button type="button" class="btn btn-link border-0 btn-block d-flex justify-content-between card-btn py-3 px-4 font-size-17 font-weight-bold text-dark" data-toggle="collapse" data-target="#basicsCollapseDetail">
                        <?php echo e(__("Booking Detail")); ?>

                        <span class="card-btn-arrow font-size-14 text-dark"><i class="fa fa-chevron-down"></i></span>
                    </button>
                </h5>
            </div>
            <div id="basicsCollapseDetail" class="collapse show" data-parent="#basicsAccordionBooking">
                <div class="card-body px-4 pt-0">
                    <ul class="list-unstyled font-size-1 mb-0 font-size-16">
                        <?php if($booking->start_date): ?>
                            <li class="d-flex justify-content-between py-2">
                                <div class="label"><?php echo e(__('Start date:')); ?></div>
                                <div class="val">
                                    <?php echo e(display_date($booking->start_date)); ?>

                                </div>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <div class="label"><?php echo e(__('End date:')); ?></div>
                                <div class="val">
                                    <?php echo e(display_date($booking->end_date)); ?>

                                </div>
                            </li>
                        <?php endif; ?>
                        <li class="d-flex justify-content-between py-2">
                            <div class="label"><?php echo e(__('Days:')); ?></div>
                            <div class="val">
                                <?php echo e($booking->duration_days); ?>

                            </div>
                        </li>
                        <?php if($meta = $booking->number): ?>
                            <li class="d-flex justify-content-between py-2">
                                <div class="label"><?php echo e(__('Number:')); ?></div>
                                <div class="val">
                                    <?php echo e($meta); ?>

                                </div>
                            </li>
                        <?php endif; ?>
                        <li class="flex-wrap">
                            <div class="flex-grow-0 flex-shrink-0 w-100">
                                <p class="text-center">
                                    <a data-toggle="modal" data-target="#detailBookingDate<?php echo e($booking->code); ?>" aria-expanded="false"
                                       aria-controls="detailBookingDate<?php echo e($booking->code); ?>">
                                        <?php echo e(__('Detail')); ?> <i class="icofont-list"></i>
                                    </a>
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card rounded-0 border-top-0 border-left-0 border-right-0">
            <div class="card-header card-collapse bg-transparent border-0" id="basicsHeadingFour">
                <h5 class="mb-0">
                    <button type="button" class="btn btn-link border-0 btn-block d-flex justify-content-between card-btn py-3 px-4 font-size-17 font-weight-bold text-dark" data-toggle="collapse" data-target="#basicsCollapsePayment">
                        <?php echo e(__("Payment")); ?>

                        <span class="card-btn-arrow font-size-14 text-dark"><i class="fa fa-chevron-down"></i></span>
                    </button>
                </h5>
            </div>
            <div id="basicsCollapsePayment" class="collapse show">
                <div class="card-body px-4 pt-0">
                    <ul class="list-unstyled font-size-1 mb-0 font-size-16">
                        <?php
                            $price_item = $booking->total_before_extra_price;
                        ?>
                        <?php if(!empty($price_item)): ?>
                            <li class="d-flex justify-content-between py-2">
                                <div class="label"><?php echo e(__('Rental price')); ?>

                                </div>
                                <div class="val">
                                    <?php echo e(format_money( $price_item)); ?>

                                </div>
                            </li>
                        <?php endif; ?>
                        <?php $extra_price = $booking->getJsonMeta('extra_price') ?>
                        <?php if(!empty($extra_price)): ?>
                            <li class="d-flex justify-content-between py-2">
                                <div class="font-size-16 font-weight-bold">
                                    <?php echo e(__("Extra Prices:")); ?>

                                </div>
                            </li>
                            <?php $__currentLoopData = $extra_price; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="d-flex justify-content-between py-2">
                                    <div class="label"><?php echo e($type['name_'.$lang_local] ?? __($type['name'])); ?>:</div>
                                    <div class="val">
                                        <?php echo e(format_money($type['total'] ?? 0)); ?>

                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php
                            $list_all_fee = [];
                            if(!empty($booking->buyer_fees)){
                                $buyer_fees = json_decode($booking->buyer_fees , true);
                                $list_all_fee = $buyer_fees;
                            }
                            if(!empty($vendor_service_fee = $booking->vendor_service_fee)){
                                $list_all_fee = array_merge($list_all_fee , $vendor_service_fee);
                            }
                        ?>
                        <?php if(!empty($list_all_fee)): ?>
                            <?php $__currentLoopData = $list_all_fee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $fee_price = $item['price'];
                                    if(!empty($item['unit']) and $item['unit'] == "percent"){
                                        $fee_price = ( $booking->total_before_fees / 100 ) * $item['price'];
                                    }
                                ?>
                                <li class="d-flex justify-content-between py-2">
                                    <div class="font-size-16 font-weight-bold">
                                        <?php echo e(__("Fee:")); ?>

                                    </div>
                                </li>
                                <li class="d-flex justify-content-between py-2">
                                    <div class="label">
                                        <?php echo e($item['name_'.$lang_local] ?? $item['name']); ?>

                                        <i class="icofont-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo e($item['desc_'.$lang_local] ?? $item['desc']); ?>"></i>
                                        <?php if(!empty($item['per_person']) and $item['per_person'] == "on"): ?>
                                            : <?php echo e($booking->total_guests); ?> * <?php echo e(format_money( $fee_price )); ?>

                                        <?php endif; ?>
                                    </div>
                                    <div class="val">
                                        <?php if(!empty($item['per_person']) and $item['per_person'] == "on"): ?>
                                            <?php echo e(format_money( $fee_price * $booking->total_guests )); ?>

                                        <?php else: ?>
                                            <?php echo e(format_money( $fee_price )); ?>

                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if ($__env->exists('Coupon::frontend/booking/checkout-coupon')) echo $__env->make('Coupon::frontend/booking/checkout-coupon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <li class="d-flex justify-content-between py-2">
                            <div class="label"><?php echo e(__("Total:")); ?></div>
                            <div class="val"><?php echo e(format_money($booking->total)); ?></div>
                        </li>
                        <?php if($booking->status !='draft'): ?>
                            <li class="d-flex justify-content-between py-2">
                                <div class="label"><?php echo e(__("Paid:")); ?></div>
                                <div class="val"><?php echo e(format_money($booking->paid)); ?></div>
                            </li>
                            <?php if($booking->paid < $booking->total ): ?>
                                <li class="d-flex justify-content-between py-2">
                                    <div class="label"><?php echo e(__("Remain:")); ?></div>
                                    <div class="val"><?php echo e(format_money($booking->total - $booking->paid)); ?></div>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php echo $__env->make('Booking::frontend/booking/checkout-deposit-amount', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $dateDetail = $service->detailBookingEachDate($booking); ?>
<div class="modal fade" id="detailBookingDate<?php echo e($booking->code); ?>" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center"><?php echo e(__('Detail')); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="review-list list-unstyled">
                    <li class="mb-3 pb-1 border-bottom">
                        <h6 class="label text-center font-weight-bold mb-1"></h6>
                        <div>
                            <?php if ($__env->exists("Car::frontend.booking.detail-date",['rows'=>$dateDetail,'number'=>$booking->number])) echo $__env->make("Car::frontend.booking.detail-date",['rows'=>$dateDetail,'number'=>$booking->number], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                        <div class="d-flex justify-content-between font-weight-bold px-2">
                            <span><?php echo e(__("Total:")); ?></span>
                            <span><?php echo e(format_money(array_sum(\Illuminate\Support\Arr::pluck($dateDetail,['price']))*$booking->number)); ?></span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/earntbpk/public_html/themes/Mytravel/Car/Views/frontend/booking/detail.blade.php ENDPATH**/ ?>