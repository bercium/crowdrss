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


<div class="row">
    <div class="columns medium-3">
        <ul class="side-nav" role="navigation" title="Link List">
            <li role="menuitem" class="<?php if ($selected_cat == 'All') echo "active"; ?>"><a href="<?php echo Yii::app()->createUrl("crowdfunding-sites"); ?>">All</a></li>
            <?php foreach ($categories as $cat) {?>
            <li role="menuitem" class="<?php if ($selected_cat == $cat['category']) echo "active"; ?>" style="border-top:1px solid #ddd;"><span class="right label secondary" ><?php echo $cat['c']; ?></span><a href="<?php echo Yii::app()->createUrl("crowdfunding-sites/".urlencode($cat['category'])); ?>"><?php echo $cat['category']; ?></a></li>
            <?php } ?>
        </ul>
        
    </div>
    <div class="columns medium-9">
        
         <form action="get" class="mt15">
              <div class="row collapse">
                <div class="small-10  medium-11 columns">
                    <input type="text" name="q" placeholder="Fast search...">
                </div>
                <div class="small-2 medium-1 columns">
                    <button type="submit" class="button postfix"><i class="fa-search fa"></i></button>
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
                <img src="<?php echo getLinkIcon($site['link']); ?>"> <a href="<?php echo $site['link']; ?>" target="_blank"  data-tooltip aria-haspopup="true" class="" title="<?php echo $site['title']; if(!empty($site['keywords'])) echo "<br /><strong>Keywords: </strong>".$site['keywords']; ?>"><?php echo trim_text($site['title'],18); ?></a>
                <?php if(strtotime($site['time_created']) > strtotime('-2 weeks') ){ ?><span class="label alert round" style="margin-left:5px;padding:0.15rem 0.3rem;">new</span><?php } ?>
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
                <img src="<?php echo getLinkIcon($site['link']); ?>"> <a href="<?php echo $site['link']; ?>" target="_blank"  data-tooltip aria-haspopup="true" class="" title="<?php echo $site['title']; if(!empty($site['keywords'])) echo "<br /><strong>Keywords: </strong>".$site['keywords']; ?>"><?php echo trim_text($site['title'],18); ?></a>
                <?php if(strtotime($site['time_created']) > strtotime('-2 weeks') ){ ?><span class="label alert round" style="margin-left:5px;padding:0.15rem 0.3rem;">new</span><?php } ?>
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
                <img src="<?php echo getLinkIcon($site['link']); ?>"> <a href="<?php echo $site['link']; ?>" target="_blank"  data-tooltip aria-haspopup="true" class="" title="<?php echo $site['title']; if(!empty($site['keywords'])) echo "<br /><strong>Keywords: </strong>".$site['keywords']; ?>"><?php echo trim_text($site['title'],18); ?></a>
                <?php if(strtotime($site['time_created']) > strtotime('-2 weeks') ){ ?><span class="label alert round" style="margin-left:5px;padding:0.15rem 0.3rem;">new</span><?php } ?>
            </li>
        
    
        <?php } ?>
        </ul>
        <?php } ?>
    </div>
</div>

<div class="mt50"></div>