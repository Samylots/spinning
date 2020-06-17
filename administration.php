<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 14:28
 */

include( 'php/functions.php' );

$site = new Site();
$site->addModule('modules/sessions');
$site->addModule('modules/activities');
$site->addModule('modules/activityTypes');
$site->addModule('modules/packages');
$site->addModule('modules/discounts');
$site->addModule('modules/taxes');
$site->addModule('modules/companies');
$site->addModule('modules/members');
//$site->addModule('modules/memberTypes');
$site->addModule('modules/reports');
$site->sidebar->addOption('Retour','index.php');
$site->sidebar->addOption('Sessions', 'Sessions', 'loadSessions', true);
$site->sidebar->addOption('Cours', 'Courses', 'loadActivities');
$site->sidebar->addOption('Activités', 'Activites', 'loadActivityTypes');
$site->sidebar->addOption('Forfaits', 'Packages', 'loadPackages');
$site->sidebar->addOption('Rabais', 'Discounts', 'loadDiscounts');
$site->sidebar->addOption('Taxes', 'Taxes', 'loadTaxes');
$site->sidebar->addOption('Compagnies', 'Companies', 'loadCompanies');
$site->sidebar->addOption('Membres', 'Members', 'loadMembers');
$site->sidebar->addOption('Rapports','Reports', 'showAllReports');
//$site->sidebar->addOption('Site Web', 'administrationWebSite.php');
$site->toolbar->setTitle('Administration général');
$site->start();
$site->end('<script>
				loadSessions(function (html) {
					$.when(setContent(html)).done(toggleLoadingDown);
				});
			</script>');
?>
