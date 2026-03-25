<div class="tab-pane mt-3 fade active show" id="general" role="tabpanel" aria-labelledby="general-tab">
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <form action="<?php echo e(getAdminPanelUrl()); ?>/users/groups/<?php echo e(!empty($group) ? $group->id.'/update' : 'store'); ?>" method="Post">
                <?php echo e(csrf_field()); ?>


                <div class="form-group">
                    <label><?php echo e(trans('admin/main.name')); ?></label>
                    <input type="text" name="name"
                           class="form-control  <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           value="<?php echo e(!empty($group) ? $group->name : old('name')); ?>"/>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback">
                        <?php echo e($message); ?>

                    </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group ">
                    <label><?php echo e(trans('admin/main.user_group_commission_rate')); ?></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>

                        <input type="number"
                               name="commission"
                               class="spinner-input form-control text-center <?php $__errorArgs = ['commission'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(!empty($group) ? $group->commission : old('commission')); ?>"
                               placeholder="<?php echo e(trans('admin/main.user_group_commission_rate_placeholder')); ?>" maxlength="3" min="0" max="100">

                        <?php $__errorArgs = ['commission'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback">
                            <?php echo e($message); ?>

                        </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="text-muted text-small mt-1"><?php echo e(trans('admin/main.user_group_commission_rate_hint')); ?></div>
                </div>

                <div class="form-group ">
                    <label><?php echo e(trans('admin/main.user_group_discount_rate')); ?></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                        <input type="number"
                               name="discount"
                               class="form-control spinner-input text-center <?php $__errorArgs = ['discount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(!empty($group) ? $group->discount : old('discount')); ?>"
                               placeholder="<?php echo e(trans('admin/main.user_group_discount_rate_placeholder')); ?>" maxlength="3" min="0" max="100">
                        <?php $__errorArgs = ['discount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback">
                            <?php echo e($message); ?>

                        </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="text-muted text-small mt-1"><?php echo e(trans('admin/main.user_group_discount_rate_hint')); ?></div>
                </div>


                <div class="form-group">
                    <label class="input-label d-block"><?php echo e(trans('admin/main.users')); ?></label>
                    <select name="users[]" multiple="multiple" class="form-control search-user-select2"
                            data-search-option="for_user_group"
                            data-placeholder="<?php echo e(trans('public.search_user')); ?>">

                        <?php if(!empty($userGroups) and $userGroups->count() > 0): ?>
                            <?php $__currentLoopData = $userGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($userGroup->user_id); ?>" selected><?php echo e($userGroup->user->full_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="status" value="inactive">
                        <input type="checkbox" name="status" id="preloadingSwitch" value="active" <?php echo e((!empty($group) and $group->status == 'active') ? 'checked="checked"' : ''); ?> class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="preloadingSwitch"><?php echo e(trans('admin/main.active')); ?></label>
                    </label>
                </div>

                <div class=" mt-4">
                    <button class="btn btn-primary"><?php echo e(trans('admin/main.submit')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\backend\resources\views/admin/users/groups/tabs/general.blade.php ENDPATH**/ ?>