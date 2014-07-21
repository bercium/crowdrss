<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;



?>


  
		<div>
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

		<div>
      <a id="subscribe" class="anchor"></a>
      <div class="row">
        <div class="columns large-12 large-centered">
          
          <form data-abide>
          
          <h1>Chose a platform</h1>
          <p>Chose the platforms you wish to get your news from:<p>
          <div class="row" data-equalizer>

            <div class="columns medium-4 large-4" data-equalizer-watch>
              <label for="platformAll">
                <div class="panel radius text-center" style="height:150px;"><h2> All platforms</h2></div>
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
          
          <h1>Categories</h1>
          <p>Chose all categories you woud like to follow:<p>
            
          <div class="row" data-equalizer>

            <div class="columns medium-4 large-4" data-equalizer-watch>
              
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
            
            <div class="columns medium-4 large-4 text-center" data-equalizer-watch>
              
              
            </div>

            <div class="columns medium-4 large-4 text-center" data-equalizer-watch>
              
            </div>

          </div>
          <hr>
          
          
          
          <h1>Subscribe</h1>
          <p>link will be sent to your email:<p>
            
          <div class="email-field">
            <label>Email *
              <input type="email" required>
            </label>
            <small class="error">An email address is required.</small>
          </div>
          
          
          <button type="submit" class="success radius">Submit</button>
        
          </form>
        </div>
        
      </div>
    </div>