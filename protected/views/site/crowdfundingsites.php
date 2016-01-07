<div class="mt50"></div>
<div class="row">
    <div class="columns medium-3">
        <ul class="side-nav" role="navigation" title="Link List">
            <li role="menuitem"><a href="<?php echo Yii::app()->createUrl("crowdfunding-sites"); ?>">All</a></li>
            <?php foreach ($categories as $cat) {?>
                <li role="menuitem"><a href="<?php echo Yii::app()->createUrl("crowdfunding-sites/".$cat['category']); ?>"><?php echo $cat['category']; ?></a><hr></li>
            <?php } ?>
        </ul>
    </div>
    <div class="columns medium-9">
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
        
            <li><img src="<?php echo getLinkIcon($site['link']); ?>"> <a href="<?php echo $site['link']; ?>" target="_blank"  data-tooltip aria-haspopup="true" class="has-tip" title="<?php echo $site['title']."<br />".$site['keywords']; ?>"><?php echo trim_text($site['title'],20); ?></a></li>
        
    
        <?php } ?>
        </ul>
                
    </div>
</div>

<div class="mt50"></div>