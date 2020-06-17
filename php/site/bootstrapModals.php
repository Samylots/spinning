<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 15:02
 */
?>

<!-- Modal -->
<div class="modal fade draggable" id="modal" role="alertdialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content" role="document">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 id="modalTitle" class="modal-title">Modal Header</h4>
			</div>
			<div id="modalContent" class="modal-body">
				<p>Some text in the modal.</p>
			</div>
			<div class="modal-footer">
				<button id="OkButton" type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>
<!-- Yes No Modal -->
<div class="modal fade draggable" id="yesNoDialog" role="alertdialog">
	<div class="modal-dialog" role="document">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 id="yesNoDialogTitle" class="modal-title">Modal Header</h4>
			</div>
			<div id="yesNoDialogContent" class="modal-body">
				<p>Some text in the modal.</p>
			</div>
			<div class="modal-footer">
				<button id="yesNoDialogYesOption" type="button" class="btn btn-default confirm" data-dismiss="modal">Oui</button>
				<button id="yesNoDialogNoOption" type="button" class="btn btn-default cancel" data-dismiss="modal">Non</button>
			</div>
		</div>
	</div>
</div>
<!-- Tooltip -->
<div class="modal-dialog customTooltip" id="customTooltip">
	<!-- Content-->
	<div class="modal-content" role="document">
		<div class="modal-header">
			<h4 id="customTooltipTitle" class="modal-title">Modal Header</h4>
		</div>
		<div id="customTooltipContent" class="modal-body">
			<p>Some text in the modal.</p>
		</div>
	</div>
</div>

