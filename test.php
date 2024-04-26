<?php

require ("header.php");

if($user_class->admin < 1){
  exit();
}
?>
<style>:root{
	--main-color-rgb: 51,51,51;
	--main-color: #333;
	--main2-color: #222;
	--main3-color: #999;
	--border-radius: 5px;
	--font-color: #999;
	--font-hover-color: #FFF;
	
	--premium-tier: 255, 217, 0; /* gold */
	--premium-tier: 224, 17, 95; /* ruby */
	--free-tier: 192, 192, 192;
}
.img-fluid{
	max-width: 100%;
}
.mb-1 {
    margin-bottom: 0.25rem!important;
}
.mb-2 {
    margin-bottom: 0.5rem!important;
}
.mb-3{
	margin-bottom: 1rem!important;
}
.text-end {
    text-align: right!important;
}





.passPanel{
	margin: 0 auto;
	max-width: 300px;
	padding: 1rem;
	text-align: center;
	font-weight: 700;
	border-radius: var(--border-radius);
	background-color: var(--main-color);
	border: 2px solid var(--main-color);
	background-image: linear-gradient(to bottom, transparent, rgba(0, 0, 0, .3));
}
.passInfo{
	margin: 0 auto;
	color: var(--font-hover-color);
	color: #FFF;
	text-align: center;
	font-weight: 700;
}
.passPanel .seasonName{
	/* color: gold; */
	color: rgb(var(--premium-tier));
}
.passPanel strong{
	color: var(--font-hover-color);
}
.passBtn{
	--color: var(--font-hover-color);
	text-decoration: none;
	display: inline-flex;
	margin-left: 10px;
	padding: 8px 15px;
	background-color: var(--main2-color);
	border: 2px solid var(--color);
	border-radius: var(--border-radius);
	box-shadow: 0 0 5px rgba(0,0,0,.3);
	color: var(--font-hover-color);
	font-weight: 600;
    line-height: normal;
	text-align: center;
	text-shadow: 0 0 1px #000;
	text-transform: capitalize;
	text-decoration: none !important;
    white-space: nowrap;
    vertical-align: middle;
	box-shadow: 0 0 5px rgba(0,0,0,.3);
	text-decoration: none;
	background-image: linear-gradient(to bottom, transparent, rgba(0,0,0,.5));
}
.passBtn:focus,
.passBtn:active,
.passBtn:hover{
	color: #FFF;
	background-color: var(--color);
}
.passBtn>span{
	align-self: center;
}
.passBtn>span>span{
	font-size: 11px;
	font-weight: normal;
}
.passBtn.buypass{
	--color: rgb(121, 16, 182);
}
.passBtn.buy{
	--color: rgb(19, 98, 201);
}
.passBtn.collect{
	height: 50px;
	--color: rgb(25, 128, 0);
}
.passBtn.collect.disabled{
	filter: grayscale(100%);
	pointer-events: none;
	cursor: not-allowed;
}
.collected{
	color: rgb(25, 128, 0) !important;
}
.pass_free{
	color: rgb(192, 192, 192);
	display: inline-block;
	min-width: 140px;
}
.pass_premium{
	color: rgb(255, 217, 0);
	display: inline-block;
	min-width: 140px;
}

.seasonPass_container{
	position: relative;
	display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	width: 100%;
	max-width: 100%;
}
.seasonPass_container > .title{
	flex: 0 0 40px;
	padding-right: 5px;
	max-width: 40px;
	padding-top: 70px;
}
.seasonPass_container > .title .current_tier{
	font-size: 15px;
	color: var(--font-hover-color);
	height: 50px;
	line-height: normal;
	position: absolute;
	width: 100%;
	top: 0;
	display: flex;
	flex-direction: row;
}
.seasonPass_container > .title .current_tier>div:first-of-type{
	flex: 0 1 80px;
	text-align: left;
}
.seasonPass_container > .title .current_tier>div:last-of-type{
	flex: 1 1 auto;
	padding: 10px 0;
}
.seasonPass_container > .title .current_tier strong{
	color: silver;
	font-size: 140%;
	display: inline-block;
	margin-left: 5px;
}
.seasonPass_container > .title .current_tier.premium strong{
	color: rgb(var(--premium-tier));
}
.seasonPass_container > .title .current_tier .bar_holder{
	box-shadow: 0 0 5px rgba(0,0,0,.3);
	background-color: var(--main-color);
	border-radius: var(--border-radius);
	position: relative;
	overflow: hidden;
	width: 100%;
}
.seasonPass_container > .title .current_tier .bar_holder > .bar{
	height: 7px;
	transition: width;
	transition-duration: .5s;
	transition-timing-function: cubic-bezier(0.36, 0.55, 0.63, 0.48);
	max-width: 100%;
	background-color: rgb(var(--free-tier));
	background-image:
		repeating-linear-gradient(90deg,transparent,transparent 2px,rgba(0, 0, 0, .1) 2px,rgba(0, 0, 0, .1) 3px),
		linear-gradient(to bottom, transparent, rgba(0, 0, 0, .3));
}
.seasonPass_container > .title .current_tier.premium .bar_holder > .bar{
	background-color: rgb(var(--premium-tier));
}
.seasonPass_container > .title .box{
	background-color: rgb(var(--free-tier));
	border-radius: var(--border-radius);
	padding: 10px 5px;
	min-height: 120px;
	max-height: 120px;
	box-shadow: 0 0 5px rgba(0,0,0,.3);
	background-image: linear-gradient(to right, transparent, rgba(0,0,0,.5));
}
.seasonPass_container > .title .box .image{
	position: relative;
	text-align: center;
	display: none;
	width: 70px;
	margin: 0 auto;
	background: var(--main2-color);
	border-radius: var(--border-radius);
	padding: 5px;
}
.seasonPass_container > .title .box.locked>.image>span{
	color: #FFF;
	position: absolute;
	top: 0;
	right: 0;
}
.seasonPass_container > .title .box .text{
	font-size: 14px;
	font-weight: 600;
	letter-spacing: 1px;
	color: #000;
	text-align: center;
    transform-origin: bottom right;
    transform: rotate(270deg);
	padding-top: 20px;
	display: flex;
	align-items: center;
	justify-content: center;
}
.seasonPass_container > .title .box.premium{
	background-color: rgb(var(--premium-tier));
}
.seasonPass_container > .title .box.premium .text{
	color: #000;
}
.tier_number.locked{
	background: rgba(var(--main-color-rgb), .3);
}
.tier_number{
	padding: 3px 0;
	background: rgba(var(--main-color-rgb), .9);
	text-align: center;
	font-weight: 700;
	color: var(--font-hover-color);
}
.seasonPass_container > .tiers_container::-webkit-scrollbar{
	height: 15px;
	background: var(--main-color);
}
.seasonPass_container > .tiers_container::-webkit-scrollbar-thumb {
	background-color: var(--main3-color);
}
.seasonPass_container > .tiers_container{
	flex: 1 1 0;
	width: 100%;
	max-width: 100%;
	overflow: auto;
	border: 2px solid var(--main3-color);
	border-radius: var(--border-radius);
	background: var(--main2-color);
	display: flex;
	flex-direction: row;
	margin-top: 40px;
}
.seasonPass_container > .tiers_container .tiers_wrapper{
	display: flex;
	flex-direction: column;
}
.seasonPass_container > .tiers_container .tiers_wrapper > .premium{
	background-color: rgba(var(--premium-tier), .05);
}
.seasonPass_container > .tiers_container .tiers_wrapper:first-of-type .tier{
	margin-left: 10px;
}
.seasonPass_container > .tiers_container .tiers_wrapper:last-of-type .tier{
	margin-right: 10px;
}
.seasonPass_container > .tiers_container .tier.locked{
	opacity: .3;
}
.seasonPass_container > .tiers_container .tier{
	min-width: 80px;
	max-width: 80px;
	min-height: 100px;
	max-height: 100px;
	border-radius: var(--border-radius);
	padding: 2px;
	background-image: linear-gradient(0deg, rgba(var(--free-tier), 1), transparent, #000, transparent, rgba(var(--free-tier), 1));
	border: 1px solid rgba(var(--free-tier), .5);
	margin: 10px 5px;
}
.seasonPass_container > .tiers_container .tier_box{
	min-height: 94px;
	max-height: 94px;
	padding: 5px;
	border: 1px solid rgba(var(--free-tier), .5);
	border-radius: var(--border-radius);
	background-color: var(--main2-color);
}
.seasonPass_container > .tiers_container .tier.premium{
	background-image: linear-gradient(0deg, rgba(var(--premium-tier), 1), transparent, #000, transparent, rgba(var(--premium-tier), 1));
	border-color: rgba(var(--premium-tier), .5);
}
.seasonPass_container > .tiers_container .tier.premium .tier_box{
	border-color: rgba(var(--premium-tier), .5);
}
.seasonPass_container > .tiers_container .tier .image{
	position: relative;
	border: 1px solid var(--main-color);
	min-height: 50px;
	max-height: 50px;
    overflow: hidden;
    border-radius: var(--border-radius);
	/* display: flex; */
	/* align-items: center; */
	padding: 3px;
	background-color: var(--main2-color);
}
.seasonPass_container > .tiers_container .tier .image img{
	width: 100%;
}
.seasonPass_container > .tiers_container .tier .image img+div{
	position: absolute;
	right: 0;
	bottom: 0;
	color: #FFF;
	font-size: 11px;
	line-height: normal;
	text-align: center;
	text-shadow: 0 0 1px #000;
	width: 100%;
	background-color: var(--main-color);
}
.seasonPass_container > .tiers_container .tier .image>span{
	color: #FFF;
	position: absolute;
	top: 0;
	right: 0;
}
.seasonPass_container > .tiers_container .tier .text{
	color: #FFF;
	font-size: 12px;
	text-align: center;
	vertical-align: middle;
	line-height: normal;
	padding-top: 5px;
}
@media (min-width: 767px) {
	.seasonPass_container > .title{
		flex: 0 0 100px;
		padding-right: 10px;
		max-width: 100px;
		padding-top: 0;
	}
	
	.seasonPass_container > .title .box{
		background-image: linear-gradient(to bottom, transparent, rgba(0,0,0,.5));
	}
	.seasonPass_container > .title .box .image{
		display: block;
	}
	.seasonPass_container > .tiers_container{
		margin-top: 20px;
	}
	.seasonPass_container > .title .current_tier{
		flex-direction: column;
		position: relative;
	}
	.seasonPass_container > .title .current_tier>div:first-of-type{
		text-align: center;
	}
	.seasonPass_container > .title .box .text{
		padding-top: 10px;
		display: block;
		transform: rotate(0deg);
	}
}</style>



<?php
$db->query("SELECT * FROM baltlepass_users WHERE userid = ".$user_class->id);
$db->execute();
if($db->num_rows() < 1){
  $db->query("INSERT INTO baltlepass_users (userid) VALUES (".$user_class->id.")");
  $db->execute();
  $db->query("SELECT * FROM baltlepass_users WHERE userid = ".$user_class->id);
  $db->execute();
}
$bp = $db->fetch_row(true);
?>
<div class="row g-3 mb-3">
			<div class="col-xl-6">
				<div class="passPanel mb-3">
					<h2 class="seasonName">Chaos Pass</h2>
					<div class="text-center mb-3">
						1/1/2000 - 2/2/2000
					</div>
				</div>
			</div>
			<div class="col-xl-6">
				<div class="passInfo">
					<p>Here you can by the chaos pass, the chaos pass will change each month, there is two types of tiers free and premium</p>
				</div>
			</div>
		</div>

		<div class="text-end mb-2">
			<a href="?page=seasonPass&action=collect&_CSFR={_CSFRToken}" class="passBtn collect {#unless current.canCollect}disabled{/unless} user-select-none">
				<span>Collect Rewards</span>
			</a>
			<!-- if not max level-->
				<a href="?page=seasonPass&action=buy_levels&_CSFR={_CSFRToken}" class="passBtn buy user-select-none">
					<span>Purchase 5 Level<br><span>{number_format premium5levelCost} {_setting "pointsName"}</span></span>
				</a>
				
			<!-- if not max level end-->
				<a href="?page=seasonPass&action=buy_premium&_CSFR={_CSFRToken}" class="passBtn buy" user-select-none>
					<span>Purchase Premium Pass<br><span>{number_format premiumCost} {_setting "pointsName"}</span></span>
				</a>
			{/if}
		</div>
<?php 
$db->query("SELECT * FROM battlepass");
$db->execute();
$ro = $db->fetch_row();
?>
		<div class="seasonPass_container">
			<div class="title">
				<div class="current_tier {#if premium}premium{/if}">
					<div>Tier <strong><?= $bp['tier']; ?></strong></div>
					<div><div class="bar_holder"><div class="bar" style="width: {current.exp_perc}%;"></div></div></div>
				</div>
				<div class="box free">
					<div class="image">
						<img src="modules/installed/seasonPass/images/free.png" alt="Free" class="img-fluid">
					</div>
					<div class="text">Free</div>
				</div>
				<div class="mb-2"></div>
				<div class="box premium {#unless premium}locked{/unless}">
					<div class="image">
						{#unless premium}{>lock}{/unless}
						<img src="modules/installed/seasonPass/images/premium.png" alt="Premium" class="img-fluid">
					</div>
					<div class="text">Premium</div>
				</div>
			</div>
			<div class="tiers_container" data-current-tier="{current.tier}">
				<?php foreach($ro AS $row):?>
					<div class="tiers_wrapper" data-tier="{tier}"> 
						<div class="tier_number {#if locked}locked{/if}">Tier {tier}</div>
						<div class="mb-1"></div>

						<div class="free">
							<div class="tier {#if locked}locked{/if}">
								<div class="tier_box"{#if free.exist} title="<b>{free.text}{#if free.qty} x{number_format free.qty}{/if}</b>{/if}">
									<div class="image">
										<?php if($row['paid'] < 1): ?>
									
											<a href="{free.link}"><img src="{free.image}"><?= $row['type'];?> <div class="qty">x<?= $row['qty'];?> </div></a>
							
									</div>
									<div class="text"><?= $row['type'] .' x '. $row['qty']; ?></div>
                  <?php endif;?>
								</div>
                
							</div>
              
						</div>
            

						<div class="mb-2"></div>

						<div class="premium">
							<div class="tier premium {#if ../premium}{#if locked}locked{/if}{else}locked{/if}">
								<div class="tier_box"{#if premium.exist} title="<b>{premium.text}{#if premium.qty} x{number_format premium.qty}{/if}</b>{/if}">
									<div class="image">
                  <?php if($row['paid'] > 0): ?>
								
											<a href="{premium.link}"><img src="{premium.image}"><?= $row['type'];?><div class="qty">x<?= $row['type'];?></div></a>
				
									</div>
									<div class="text"><?= $row['type'] .' x '. $row['qty']; ?></div>
                  <?php endif;?>
								</div>
							</div>
						</div>
					</div>
          
				<?php endforeach; ?>
			</div>
		</div>