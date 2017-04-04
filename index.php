<?php

//Get entire database to javascript
$heroScript = getData("hero");
$skillScript = getData("skill");
$prereqScript = getData("skill_prereq");
$heroskillScript = getData("hero_skill");

function getData($table){
	require("dbHeroes.php");
	$stmt = $db->prepare("select * from " . $table);
	$stmt->execute();

	$js = "[";
	while($result = $stmt->fetch()){
		$js .= "{";
		foreach ($result as $var => $value) {
			if(!is_numeric($var)){
				if(!is_numeric($value)){
					$value = str_replace("\"", "\\\"", $value);
					$value = "\"" . $value . "\"";
				}
				$js .= "\"" . $var . "\":" . $value . ",";
			}
		}
		$js = trim($js,",") . "},";
	}
	$js = trim($js,",") . "]";
	return $js;
}
?>

<!doctype html>
<html>

<head>
	<title>FEH Mass Duel Simulator</title>
	<link rel="stylesheet" type="text/css" href="style.css?v=2"/>
	<link rel="shortcut icon" href="favicon.ico" />

	<script type="text/javascript" src="/libraries/jquery.js"></script>

	<script>
		heroes = <?php echo $heroScript;?>;
		skills = <?php echo $skillScript;?>;
		prereqs = <?php echo $prereqScript;?>;
		heroskills = <?php echo $heroskillScript;?>;
	</script>

	<script type="text/javascript" src="activehero.js?v=3"></script>
	<script type="text/javascript" src="code.js?v=2"></script>

	<!-- Google Tag Manager -->
	<script>
	dataLayer = [{
		"pageType":"app"
	}];

	(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-TWPWXN5');</script>
	<!-- End Google Tag Manager -->

</head>

<body>
	<div id="frame_main">
		<div id="header"><img src="logo.png" style="margin-right:20px;"/><div style="display:inline-block;">Mass Duel Simulator!</div><div id="subtitle"></div></div>
		<div id="frame_options">
			<div id="frame_challenger">
				<div id="challenger_bar_top">
					Challenger: <select id="hero_name"></select> <span class="level_label">Lvl 40+</span><input id="challenger_merge" type="number" value=0 min=0 max=10 /><div class="bufflabel">Buff</div><div class="spurlabel">Spur</div><div class="button" id="reset_challenger" onclick="resetChallenger();">Reset</div>
				</div>
				<div id="frame_challenger_picture"><img id="challenger_picture" src="heroes/nohero.png"/><img id="weaponIcon" src="weapons/noweapon.png"/></div>
				<div id="challenger_stats" class="frame_stats">
					<div class="stat_row">HP: <span class="stat_number" id="hero_hp">-</span><div class="button boonbutton neutral" data-val=0 id="hero_boon_hp">Neutral</div></div>
					<div class="stat_row">Atk: <span class="stat_number" id="hero_atk">-</span><div class="button boonbutton neutral" data-val=0 id="hero_boon_atk">Neutral</div><input id="hero_atk_buff" class="buff_input" type="number" value=0 min=0 max=7 /><input id="hero_atk_spur" class="spur_input" type="number" value=0 min=0 max=12 /></div>
					<div class="stat_row">Spd: <span class="stat_number" id="hero_spd">-</span><div class="button boonbutton neutral" data-val=0 id="hero_boon_spd">Neutral</div><input id="hero_spd_buff" class="buff_input" type="number" value=0 min=0 max=7 /><input id="hero_spd_spur" class="spur_input" type="number" value=0 min=0 max=12 /></div>
					<div class="stat_row">Def: <span class="stat_number" id="hero_def">-</span><div class="button boonbutton neutral" data-val=0 id="hero_boon_def">Neutral</div><input id="hero_def_buff" class="buff_input" type="number" value=0 min=0 max=7 /><input id="hero_def_spur" class="spur_input" type="number" value=0 min=0 max=12 /></div>
					<div class="stat_row">Res: <span class="stat_number" id="hero_res">-</span><div class="button boonbutton neutral" data-val=0 id="hero_boon_res">Neutral</div><input id="hero_res_buff" class="buff_input" type="number" value=0 min=0 max=7 /><input id="hero_res_spur" class="spur_input" type="number" value=0 min=0 max=12 /></div>
					<div class="stat_row">Damage taken: <input id="challenger_damage" class="damage_input" type="number" value=0 min=0 max=99 /></div>
				</div>
				<div id="challenger_skills">
					<div class="skill_row"><img class="skill_picture" id="hero_weapon_picture" src="skills/weapon.png"/>Weapon: <select class="skill_select" id="hero_weapon"></select></div>
					<div class="skill_row"><img class="skill_picture" id="hero_special_picture" src="skills/special.png"/>Special: <select class="skill_select" id="hero_special"></select><div class="skill_row_right"><span id="challenger_specialcharge">0</span> Charge: <input type="number" id="challenger_precharge" class="special_precharge" value=0 min=0 max=6 /></div></div>
					<div class="skill_row"><img class="skill_picture" id="hero_a_picture" src="skills/noskill.png"/>Passive A: <select class="skill_select" id="hero_a"></select></div>
					<div class="skill_row"><img class="skill_picture" id="hero_b_picture" src="skills/noskill.png"/>Passive B: <select class="skill_select" id="hero_b"></select></div>
					<div class="skill_row"><img class="skill_picture" id="hero_c_picture" src="skills/noskill.png"/>Passive C: <select class="skill_select" id="hero_c"></select></div>
				</div>
			</div>
			<div id="frame_enemies">
				<div id="enemies_bar_top">
					Enemies: <span id="enemies_count">-</span> &nbsp;&nbsp;<span class="level_label">Lvl 40+</span><input id="enemies_merge" type="number" value=0 min=0 max=10 /><div class="bufflabel">Buff</div><div class="spurlabel">Spur</div><div class="button" id="reset_enemies" onclick="resetEnemies();">Reset</div>
				</div>
				<div id="enemies_include">
					<div id="enemies_includeheader">Include:</div>
					<div class="includebuttons_row"><div class="button wideincludebutton included" id="include_melee">Melee</div><div class="button wideincludebutton included" id="include_ranged">Ranged</div></div>
					<div class="includebuttons_row"><div class="button thinincludebutton included" id="include_red">Red</div><div class="button thinincludebutton included" id="include_blue">Blue</div><div class="button thinincludebutton included" id="include_green">Green</div><div class="button thinincludebutton included" id="include_gray">Gray</div></div>
					<div class="includebuttons_row"><div class="button wideincludebutton included" id="include_physical">Physical</div><div class="button wideincludebutton included" id="include_magical">Magical</div></div>
					<div class="includebuttons_row"><div class="button thinincludebutton included" id="include_infantry">Infantry</div><div class="button thinincludebutton included" id="include_cavalry">Cavalry</div><div class="button thinincludebutton included" id="include_flying">Flying</div><div class="button thinincludebutton included" id="include_armored">Armored</div></div>
					<div class="includebuttons_row"><div class="button wideincludebutton notincluded" id="include_staff">Healers</div><div class="button wideincludebutton included" id="include_nonstaff">Non-healers</div></div>
				</div>
				<div id="enemies_stats" class="frame_stats">
					<div class="stat_row">HP: <span class="stat_number" id="enemies_hp">-</span><div class="button boonbutton neutral" data-val=0 id="enemies_boon_hp">Neutral</div></div>
					<div class="stat_row">Atk: <span class="stat_number" id="enemies_atk">-</span><div class="button boonbutton neutral" data-val=0 id="enemies_boon_atk">Neutral</div><input id="enemies_atk_buff" class="buff_input" type="number" value=0 min=0 max=7 /><input id="enemies_atk_spur" class="spur_input" type="number" value=0 min=0 max=12 /></div>
					<div class="stat_row">Spd: <span class="stat_number" id="enemies_spd">-</span><div class="button boonbutton neutral" data-val=0 id="enemies_boon_spd">Neutral</div><input id="enemies_spd_buff" class="buff_input" type="number" value=0 min=0 max=7 /><input id="enemies_spd_spur" class="spur_input" type="number" value=0 min=0 max=12 /></div>
					<div class="stat_row">Def: <span class="stat_number" id="enemies_def">-</span><div class="button boonbutton neutral" data-val=0 id="enemies_boon_def">Neutral</div><input id="enemies_def_buff" class="buff_input" type="number" value=0 min=0 max=7 /><input id="enemies_def_spur" class="spur_input" type="number" value=0 min=0 max=12 /></div>
					<div class="stat_row">Res: <span class="stat_number" id="enemies_res">-</span><div class="button boonbutton neutral" data-val=0 id="enemies_boon_res">Neutral</div><input id="enemies_res_buff" class="buff_input" type="number" value=0 min=0 max=7 /><input id="enemies_res_spur" class="spur_input" type="number" value=0 min=0 max=12 /></div>
					<div class="stat_row">Damage taken: <input id="enemies_damage" class="damage_input" type="number" value=0 min=0 max=99 /></div>
				</div>
				<div id="enemies_skills">
					<div class="enemies_row"><img class="skill_picture" id="enemies_weapon_picture" src="skills/weapon.png"/>Give weapon: <select class="skill_select_enemies" id="enemies_weapon"></select><select class="skill_overwrite" id="enemies_weapon_overwrite"><option value=0>If empty</option><option value=1>Overwrite</option></select></div>
					<div class="enemies_row"><img class="skill_picture" id="enemies_special_picture" src="skills/special.png"/>Give special: <select class="skill_select_enemies" id="enemies_special"></select><select class="skill_overwrite" id="enemies_special_overwrite"><option value=0>If empty</option><option value=1>Overwrite</option></select><div class="skill_row_right_enemies">Charge: <input type="number" id="enemies_precharge" class="special_precharge" value=0 min=0 max=6 /></div></div>
					<div class="enemies_row"><img class="skill_picture" id="enemies_a_picture" src="skills/noskill.png"/>Give passive A: <select class="skill_select_enemies" id="enemies_a"></select><select class="skill_overwrite" id="enemies_a_overwrite"><option value=0>If empty</option><option value=1>Overwrite</option></select></div>
					<div class="enemies_row"><img class="skill_picture" id="enemies_b_picture" src="skills/noskill.png"/>Give passive B: <select class="skill_select_enemies" id="enemies_b"></select><select class="skill_overwrite" id="enemies_b_overwrite"><option value=0>If empty</option><option value=1>Overwrite</option></select></div>
					<div class="enemies_row"><img class="skill_picture" id="enemies_c_picture" src="skills/noskill.png"/>Give passive C: <select class="skill_select_enemies" id="enemies_c"></select><select class="skill_overwrite" id="enemies_c_overwrite"><option value=0>If empty</option><option value=1>Overwrite</option></select></div>
				</div>
			</div>
		</div>
		<div id="frame_rules">
			<div id="rules_bar_top">
				Duel Rules
			</div>
			<div id="rules_turnorder">
				<div id="turnorder_left">
					<div id="turnorder_label">Turn order:</div>
					<div class="button turnorder_button" id="add_turn_challenger">Challenger -></div>
					<div class="button turnorder_button" id="add_turn_enemy">Enemy -></div>
				</div>
				<div id="turnorder_right">
					<div class="turn_label" id="turn_0"><span id="turn_text_0">Challenger initiates</span><div class="button turn_delete" id="turn_delete_0" onclick="deleteTurn(0);">x</div></div>
					<div class="turn_label" id="turn_1"><span id="turn_text_1">Enemy initiates</span><div class="button turn_delete" id="turn_delete_1" onclick="deleteTurn(1);">x</div></div>
					<div class="turn_label" id="turn_2" style="display:none;"><span id="turn_text_2"></span><div class="button turn_delete" id="turn_delete_2" onclick="deleteTurn(2);">x</div></div>
					<div class="turn_label" id="turn_3" style="display:none;"><span id="turn_text_3"></span><div class="button turn_delete" id="turn_delete_3" onclick="deleteTurn(3);">x</div></div>
				</div>
			</div>
			<div id="rules_other">
				<input type="checkbox" id="rules_prereqs"/> Allow inferior skills<br>
				<input type="checkbox" id="rules_threaten"/> Threaten skills take effect on first turn<br>
				<input type="checkbox" id="rules_galeforce" checked="checked" /> Initiate again if Galeforce triggers<br>
				<input type="number" min=0 max=3 id="rules_renewal" value=0 /> Starting turn for renewal<br>
			</div>
			<div id="rules_disclaimer">
				Spring heroes added, as well as some other stuff. Let's hope there aren't many bugs. Planned on next update: Custom enemy lists
			</div>
		</div>
		<div id="frame_results">
			<div id="results_bar_top">
				<div class="button" id="button_calculate" onclick="calculate();">Calculate!</div> <input type="checkbox" id="autocalculate" checked="checked"/>Auto-calculate
				<div id="results_bar_right">Sort: <select id="sort_results"><option value=1 >Best</option><option value=-1 >Worst</option></select></div>
			</div>
			<div id="results_graph_back">
				<div id="results_graph_wins"></div>
				<div id="results_graph_losses"></div>
			</div>
			<div id="winpercentages"><span class="results_stat">Wins: <span id="win_pct">-</span></span><span class="results_stat">Losses: <span id="lose_pct">-</span></span><span class="results_stat">Inconclusive: <span id="inconclusive_pct">-</span></span></div>
			<div id="results_list">
				<!--<div class="results_entry">
					<div class="results_hpbox">
						<div class="results_hplabel">HP</div>
						<div class="results_hpnums">
							<span class="results_challengerhp">30</span> &ndash; <span class="results_enemyhp">10</span>
						</div>
					</div>
					<img class="results_enemypicture"/>
					<div class="results_topline">
						<span class="results_enemyname">Ryoma</span> (<span class="results_outcome">eviscerated</span>)
					</div>
					<div class="results_bottomline">
						<span class="results_stat">HP: 0</span><span class="results_stat">Atk: 0</span><span class="results_stat">Spd: 0</span><span class="results_stat">Def: 0</span><span class="results_stat">Res: 0</span><span class="results_stat"><img class="skill_picture" src="skills/weapon.png"/>Dick bucket</span><span class="results_stat"><img class="skill_picture" src="skills/special.png"/>Dick bucket</span><span class="results_stat"><img class="skill_picture" src="skills/noskill.png"/><img class="skill_picture" src="skills/noskill.png"/><img class="skill_picture" src="skills/noskill.png"/></span>
					</div>
				</div>-->
			</div>
		</div>
		<div id="footer">Last <a href="changelog.txt">updated</a> 2017-4-3. <a href="https://www.reddit.com/user/anducrandu">Message me on reddit</a> if I need to fix something!</div>
	</div>
	<div id="frame_tooltip"></div>
	<a href="../../games"><div id="frame_sitead">&lt; Back to Games</div></a>
</body>

</html>