<?php

class HTML {

	public static function ShowItemPopup( $text, $id ) {
		return '<a type="button" data-efitem="' . $id . '" data-toggle="modal" data-target="#efInventory">' . $text . '</a>';
	}

	public static function ShowShopItemPopup( $text, $id ) {
		return "<a href='#' onclick=\"javascript:window.open( 'description_shop_item.php?id=" . $id . "', '60', 'left = 20, top = 20, width = 400, height = 400, toolbar = 0, resizable = 0, scrollbars=1' );\">" . $text . '</a>';
	}

	public static function ShowBookPopup( $text, $id ) {
		return '<a type="button" data-efisbook="true" data-efitem="' . $id . '" data-toggle="modal" data-target="#efInventory">' . $text . '</a>';
	}

	public static function ShowMessage( $text, $title = IMPORTANT_MESSAGE ) {
		return '<div class="message">
            <i class="fas fa-info-circle"></i>
            <div class="errorMessageText">
                <strong>' . $title . '</strong><br>
                ' . $text . '
            </div>
        </div>';
	}

	public static function ShowMessagewithoutdots( $text, $title = IMPORTANT_MESSAGE ) {
		return '
	    <tr>
	    	<td class="contenthead">
	    		 ' . $title . '
	    	</td>
	    </tr>
		<tr>
			<td class="contentcontent">' . $text . '</td>
		</tr>';
	}

	public static function ShowCriticalSuccessMessage( $text, $title = null, $hide = false ) {
		$hide  = ( $hide ) ? ' hide' : '';
		$title = ( $title ) ? $title : 'Critical Success!';
		return '<div class="message successMessage critical' . $hide . '">
            <i class="fas fa-exclamation-circle"></i>
            <div class="errorMessageText">
                <strong>' . $title . '</strong>
                <p>' . $text . '</p>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                confetti($(".successMessage.critical"));
            });
        </script>';
	}

	public static function ShowSuccessMessage( $text, $title = null, $hide = false ) {
		$hide  = ( $hide ) ? ' hide' : '';
		$title = ( $title ) ? $title : MESSAGE;
		return '<div class="message successMessage success' . $hide . '">
            <i class="fas fa-check-circle"></i>
            <div class="errorMessageText">
                <strong>' . $title . '</strong>
                <p>' . $text . '</p>
            </div>
        </div>';
	}

	public static function ShowFailedMessage( $text, $title = null, $hide = false ) {
		$hide  = ( $hide ) ? ' hide' : '';
		$title = ( $title ) ? $title : COM_FAILED;
		return '<div class="message errorMessage failed' . $hide . '">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="errorMessageText">
                <strong>' . $title . '</strong>
                <p>' . $text . '</p>
            </div>
        </div>';
	}

	public static function ShowStandartPopup( $elementID, $message ) {      ?>

		<script>

			$(document).ready(function () {

				$("#<?php echo $elementID; ?>").mouseover(function () {
					ddrivetip('<?php echo $message; ?>')
				});

				$("#<?php echo $elementID; ?>").mouseout(function () {
					hideddrivetip();
				});

			})

		</script>

		<?php
	}

	public static function ShowErrorMessage( $text, $title = null, $hide = false ) {
		$hide  = ( $hide ) ? ' hide' : '';
		$title = ( $title ) ? $title : ERROR_MESSAGE;
		return '<div class="message errorMessage error' . $hide . '">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="errorMessageText">
                <strong>' . $title . '</strong>
                <p>' . $text . '</p>
            </div>
        </div>';
	}
}

?>
