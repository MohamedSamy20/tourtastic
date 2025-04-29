<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/bootstrap-select.js"></script>
<script src="assets/js/jquery.slimscroll.js"></script>
<script src="assets/js/waves.js"></script>
<script src="assets/js/jquery.validate.js"></script>
<script src="assets/js/admin.js"></script>
<script src="assets/js/form-validation.js"></script>
<script src="assets/plugins/dropify/js/dropify.js"></script>

<script type="text/javascript">
	$(".clickable-row").click(function() {
		window.location = $(this).data("href");
	});
</script>

<script type="text/javascript">
	$(document).ready(function(){
            // Basic
            $('.dropify').dropify();

            $('.dropify-image').dropify({
            	messages: {
            		default : '<center>Drag and drop a image here or click</center>',
            		error   : 'Ooops, something wrong appended.'
            	},
            	error: {
            		'fileSize': '<center>The file size is too big broo ({{ value }} max).</center>',
            		'minWidth': '<center>The image width is too small ({{ value }}}px min).</center>',
            		'maxWidth': '<center>The image width is too big ({{ value }}}px max).</center>',
            		'minHeight': '<center>The image height is too small ({{ value }}}px min).</center>',
            		'maxHeight': '<center>The image height is too big ({{ value }}px max).</center>',
            		'imageFormat': '<center>The image format is not allowed ({{ value }} only).</center>',
            		'fileExtension': '<center>The file is not allowed ({{ value }} only).</center>'
            	},
            });

            $('.dropify-pdf').dropify({
            	messages: {
            		default: '<center>Drag and drop a <b>PDF</b> here or click</center>'
            	}
            });

            $('.dropify-video').dropify({
            	messages: {
            		default: '<center>Drag and drop a video here or click</center>'
            	}
            });

            $('.dropify-notification').dropify({
            	messages: {
            		default : '<center>Drag and drop a image here or click<br>(Optional)</center>',
            	}
            });

            // Used events
            var drEvent = $('#input-file-events').dropify();

            drEvent.on('dropify.beforeClear', function(event, element){
            	return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
            });

            drEvent.on('dropify.afterClear', function(event, element){
            	alert('File deleted');
            });

            drEvent.on('dropify.errors', function(event, element){
            	console.log('Has Errors');
            });

            var drDestroy = $('#input-file-to-destroy').dropify();
            drDestroy = drDestroy.data('dropify')
            $('#toggleDropify').on('click', function(e){
            	e.preventDefault();
            	if (drDestroy.isDropified()) {
            		drDestroy.destroy();
            	} else {
            		drDestroy.init();
            	}
            })
        });
</script>
