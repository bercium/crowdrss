<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;



?>

    <div class="intro pt30">
       <a id="whatIsCRSS" class="anchor"></a>
       <div class="row">
         <div class="columns large-12 text-center">
           <img src="images/logo.png" class="mt30 mb30 show-for-medium-up" >
           <img src="images/logo-small.png" class="mt30 mb30 show-for-small">
           <h1 class="title show-for-medium-up">Crowdfunding RSS</h1>
           <h1 class="title-small show-for-small">Crowdfunding RSS</h1>
         </div>
       </div>
    </div>
  
		<div class="intro-desc pb30">
      <a id="whatIsCRSS" class="anchor"></a>
      <div class="row">
        <div class="columns large-12 large-centered">
          <h1>What is crowdfunding RSS</h1>
          <p>

          Tracker is a competitive post apocalyptic <strong>board game</strong>. The game, where even the <strong>dead still have a chance</strong>...<br />
          Years after apocalypse. A group of survivors locked in battle for the astonishing <strong>power of artefacts</strong>. 
          In a constantly changing environment they must <strong>adapt</strong> their strategy and tactics. But much more is required, to be the <strong>very best</strong>.

          </p>

        </div>
      </div>
    </div>


		<div class="mt30">
      <a id="subscribe" class="anchor"></a>
      <div class="row">
        <div class="columns large-12 large-centered">
          
          <form data-abide>
          
          <h2>1. Chose platforms</h2>
          <p>Which platforms you wish to get your news from?<p>
          <div class="row" data-equalizer>

            <div class="columns medium-4 large-4" data-equalizer-watch>
                
                <label for="platformAll">
                  <div class="panel radius text-center small-8 small-offset-2" style="height:150px;">
                    <h2>All platforms</h2>
                  </div>
                </label>  
                  
                  <div class="switch round large" style="width:90px; margin-left: auto; margin-right: auto;">
                    <input id="platformAll" type="checkbox" checked name="platformGroup" onclick="uncheckPlatforms()">
                    <label for="platformAll"></label>
                  </div>
            </div>

            <div class="columns medium-4 large-4 text-center" data-equalizer-watch>
              <label for="platformKS">
                <img src="images/kickstarter.png">
              </label>
              <br /><br />
                <div class="switch round large" style="width:90px; margin-left: auto; margin-right: auto;">
                  <input id="platformKS" type="checkbox" name="platformGroup" onclick="$('#platformAll').prop('checked', false);">
                  <label for="platformKS"></label>
                </div>
            </div>

            <div class="columns medium-4 large-4 text-center" data-equalizer-watch>
              <label for="platformIGG">
                <img src="images/indiegogo.png">
              </label>
              <br /><br />
                <div class="switch round large" style="width:90px; margin-left: auto; margin-right: auto;">
                  <input id="platformIGG" type="checkbox" name="platformGroup" onclick="$('#platformAll').prop('checked', false);">
                  <label for="platformIGG"></label>
                </div>
            </div>

          </div>
          <hr>
          
          <h2>2. Chose categories</h2>
          <p>Which topics do you find interesting?<p>
            
          <div class="row" data-equalizer>

            <div class="columns medium-4" data-equalizer-watch>
              
              <div class="row">
                <div class="columns small-4">
                  <div class="switch round small">
                    <input id="k1" type="checkbox">
                    <label for="k1"></label>
                  </div>
                </div>
                <div class="columns small-8">
                    <label for="k2">text ki je full dolg</label>
                </div>
              </div>
              

              <div class="row">
                <div class="columns small-4">
                  <div class="switch round small">
                    <input id="k2" type="checkbox">
                    <label for="k2"></label>
                  </div>
                </div>
                <div class="columns small-8">
                    <label for="k2">text ki je full dolg</label>
                </div>
              </div>              
            </div>
            
            <div class="columns medium-4 text-center" data-equalizer-watch>
            </div>

            <div class="columns medium-4 text-center" data-equalizer-watch>
            </div>

          </div>
          <hr>
          
          
          
          <h2>3. Get the RSS link</h2>
          <p>We will generate a link and send it to your email address.<p>
            
          <div class="email-field">
            <label>Email *
              <input type="email" required>
            </label>
            <small>We will use your email only to send you RSS link and occasional crowdfunding project notifications. We will never sell your email address to anyone!</small>
          </div>
          
          <div style="margin-top: 30px;">
            <button type="submit" class="success radius">Subscribe to RSS</button>
          
            <button type="reset" class="secondary radius right">Reset all</button>
          </div>
            
          </form>
        </div>
        
      </div>
    </div>



		<div class="mt30 pt30 pb30 outro">
      <a id="whatIsCRSS" class="anchor"></a>
      <div class="row">
        <div class="columns large-12 large-centered">
          <h1>Neki neki</h1>
          <p>

          Tracker is a competitive post apocalyptic <strong>board game</strong>. The game, where even the <strong>dead still have a chance</strong>...<br />
          Years after apocalypse. A group of survivors locked in battle for the astonishing <strong>power of artefacts</strong>. 
          In a constantly changing environment they must <strong>adapt</strong> their strategy and tactics. But much more is required, to be the <strong>very best</strong>.

          </p>
          
        </div>
      </div>
    </div>