<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class TaxTypes extends Modules
{
	/**
	 * TaxTypes constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('taxe_types');
	}

	public function format( $tax){
		fail('SHOULD NOT SHOW TAXE TYPE');
	}

	public function adminEditForm( $tax){
		fail('YOU CANT EDIT A TAX');
	}

	public function adminNewForm(){
		parent::testRequest();
		return '<form id="form">
					<div class="inputs">
						Code:
						<input id="TaxCode" name="TaxCode" type="text" placeholder="Code de la taxe" required>
					</div>
				</form>
				<script>
					$("#form").validate({
						rules:{
							TaxCode:{ minlength : 3, maxlength : 5}
						},
						messages:{
							TaxCode:{ minlength: "Le code doit être au moins de 3 caractères de long.",
									  maxlength : "Le code ne doit pas être plus de 5 caractères de long."}
						}
					});
				</script>';
	}
}