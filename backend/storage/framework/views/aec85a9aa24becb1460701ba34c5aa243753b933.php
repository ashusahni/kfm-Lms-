<form action="/cart/storeCheckout" method="post" id="cartForm">
                            <?php echo e(csrf_field()); ?>

                            <input type="hidden" name="discount_id" value="<?php echo e($discount_id); ?>">

 </form>


<script>

 function submitForm() {
  document.getElementById("cartForm").submit();
}
submitForm() ;
</script>
<?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\backend\resources\views/api/checkout.blade.php ENDPATH**/ ?>