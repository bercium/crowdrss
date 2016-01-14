<style>
    [class*="block-grid-"] > li{
        padding:0 0.625rem 0.5rem;
    }
    .side-nav li{
        margin:0;
        font-size: 1.25rem;
    }
    .active{
        background-color: #eee;
    }
</style>
<div class="mt50"></div>

<?php $link = "?utm_source=crowdfundingrss.com&utm_medium=referral&utm_campaign=★%20Crowdfunding%20resource%20site"; ?>

<div class="row">
    <div class="columns medium-3">
        <ul class="side-nav" role="navigation" title="Link List">
            <li role="menuitem" class="<?php if ($selected_cat == 'All') echo "active"; ?>"><a trk="link_sidemenu_all" href="<?php echo Yii::app()->createUrl("crowdfunding-sites"); ?>">All</a></li>
            <?php foreach ($categories as $cat) {?>
            <li role="menuitem" class="<?php if ($selected_cat == $cat['category']) echo "active"; ?>" style="border-top:1px solid #ddd;"><span class="right label secondary" ><?php echo $cat['c']; ?></span><a trk="link_sidemenu_<?php echo $cat['category']; ?>" href="<?php echo Yii::app()->createUrl("crowdfunding-sites/".urlencode($cat['category'])); ?>"><?php echo $cat['category']; ?></a></li>
            <?php } ?>
            <?php if (!Yii::app()->user->isGuest){ ?>
            <li role="menuitem" ><a href="<?php echo Yii::app()->createUrl("crowdfunding-sites?edit"); ?>">Show hidden</a></li>
            <?php } ?>
        </ul>
        
        <div class="mt30"></div>
        
        <a href="#" data-reveal-id="suggest-modal" trk="button_view_suggestSiteSubmit"><button class="success radius small" style="width: 100%;">SUGGEST A SITE</button></a>
        
    </div>
    <div class="columns medium-9">
        
         <form method="get" class="mt15">
              <div class="row collapse">
                <div class="small-10  medium-11 columns">
                    <input type="text" name="q" placeholder="Quick search...">
                </div>
                <div class="small-2 medium-1 columns">
                    <button type="submit" trk="button_form_searchResources" class="button postfix"><i class="fa-search fa"></i></button>
                </div>
              </div>
        </form>
        
        <?php if (!empty($search)) { ?>
            <h3 data-magellan-destination="recent">Search results:</h3>
            <a name="search"></a>
            <ul class="small-block-grid-3">
            <?php foreach ($search as $site) {
                if (strpos($site['link'], "http") !== 0) $site['link'] = "http://".$site['link'];
            ?>
            <li>
                <img src="<?php echo getLinkIcon($site['link']); ?>"> <a href="<?php echo $site['link'].$link; ?>" trk="link_outsideLinks_<?php echo $site['title']; ?>"  target="_blank"  data-tooltip aria-haspopup="true" class="" title="<?php echo $site['title']; if(!empty($site['keywords'])) echo "<br /><strong>Keywords: </strong>".$site['keywords']; ?>"><?php echo trim_text($site['title'],18); ?></a>
                <?php if(strtotime($site['time_created']) > strtotime('-1 week') ){ ?><span class="label alert round" style="margin-left:5px;padding:0.15rem 0.3rem;">new</span><?php } ?>
                <?php if (!Yii::app()->user->isGuest){ ?><a href="<?php echo Yii::app()->createUrl("outsideLinks/update",array("id"=>$site->id)); ?>"><span class="label success round" style="margin-left:5px;padding:0.15rem 0.3rem;">edt</span></a> <?php }?>
            </li>
            <?php } ?>
            </ul>
            <hr>
        <?php } ?>
                
        <?php if (!empty($recent)) { ?>
            <h3 data-magellan-destination="recent">Most recent</h3>
            <a name="recent"></a>
            <ul class="small-block-grid-3">
        <?php foreach ($recent as $site) {
            if (strpos($site['link'], "http") !== 0) $site['link'] = "http://".$site['link'];
        ?>
            <li>
                <img src="<?php echo getLinkIcon($site['link']); ?>"> <a href="<?php echo $site['link'].$link; ?>" trk="link_outsideLinks_<?php echo $site['title']; ?>"  target="_blank"  data-tooltip aria-haspopup="true" class="" title="<?php echo $site['title']; if(!empty($site['keywords'])) echo "<br /><strong>Keywords: </strong>".$site['keywords']; ?>"><?php echo trim_text($site['title'],18); ?></a>
                <span class="label alert round" style="margin-left:5px;padding:0.15rem 0.3rem;">new</span>
            </li>
            <?php } ?>
            </ul>
            <hr>
        <?php } ?>
        <?php /* ?>
        <div data-magellan-expedition="fixed">
            <dl class="sub-nav">
                <?php foreach ($sub_cat as $cat) {?>
                <dd data-magellan-arrival="build"><a href="#<?php echo str_replace(' ','',$cat['sub_category']); ?> "><?php echo $cat['sub_category']; ?></a></dd>
                <?php } ?>
            </dl>
        </div>
        
        
        <?php 
        */
        if (!empty($sites)){
        $cur_sub_cat = '';
        $firsttime = true;
        foreach ($sites as $site) {
            if (strpos($site['link'], "http") !== 0) $site['link'] = "http://".$site['link'];
            
            
            if ($cur_sub_cat != $site['category'].'-'.$site['sub_category']){
                $cur_sub_cat = $site['category'].'-'.$site['sub_category'];
                
                if(!$firsttime){ ?></ul><hr><?php } ?>        
                <h3 data-magellan-destination="<?php echo str_replace(' ','',$site['sub_category']); ?>"><?php echo $site['sub_category']; ?></h3>
                <a name="<?php echo str_replace(' ','',$site['sub_category']); ?>"></a>

                <ul class="small-block-grid-3"><?php
                
                $firsttime = false;
            }
            ?>
        
            <li>
                <img src="<?php echo getLinkIcon($site['link']); ?>"> <a href="<?php echo $site['link'].$link; ?>" target="_blank" trk="link_outsideLinks_<?php echo $site['title']; ?>" data-tooltip aria-haspopup="true" class="" title="<?php echo $site['title']; if(!empty($site['keywords'])) echo "<br /><strong>Keywords: </strong>".$site['keywords']; ?>"><?php echo trim_text($site['title'],18); ?></a>
                <?php if(strtotime($site['time_created']) > strtotime('-1 week') ){ ?><span class="label alert round" style="margin-left:5px;padding:0.15rem 0.3rem;">new</span><?php } ?>
            </li>
        
    
        <?php } ?>
        </ul>
        <?php } ?>
    </div>
</div>

<div class="mt50"></div>

<?php /* We bring you curated list of crowdfunding sites worth being bookmarked. 
       If you are site owner leave your site here and we will add it to the list. */ ?>
<div id="suggest-modal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <h2 id="modalTitle">Suggest a Site</h2>
    <p class="lead">Suggest Crowdfunding site you like. We are happy to list all the good sites. 
       Twitter/Facebook post about crowdfundingrss.com would be highly appreciated.
    </p>
  
    <form method="post" class="mt30">
        Site URL<font style="color:#f04124">*</font>
        <input type="text" name="new_link" placeholder="www.crowdfunding.com" value="<?php if (!empty($post['new_link'])) echo $post['new_link']; ?>">
        
        Your email
        <input type="text" name="new_email" placeholder="my@email.com" value="<?php if (!empty($post['new_email'])) echo $post['new_email']; ?>">
        <div class="mt50"></div>
            
        <button type="submit" class="button radius" style="width:100%" trk="button_form_suggestSiteSubmit">Submit a suggestion</button>
    </form>
        
  
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>