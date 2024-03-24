<?php
include 'header.php';
?>
<div class='contenthead'><font color=red><b>Bloodbath</b></font></div>
<div class='contentcontent'>
  <div id="daily-dime" class="tab-menu-cont border-round">
         <div class="lottery-head-wrap">
            <div class="lottery-head"></div>
         </div>
         <div class="lottery-body-wrap">
            <canvas class="lottery-timer" id="canvas_daily-dime" width=174 height=174></canvas>
            <div class="lottery-body-bg"></div>
            <div class="lottery-text">
               <div class="lottery-info">
                  <div class="money">
                     $499,700                  </div>
                  <div class="tickets">
                     <span>4,997</span> tickets sold
                  </div>
                  <div class="clear"></div>
               </div>
               <div class="timer-title">
                  winner drawn in...
               </div>
               <div class="timer">
                                    <input type="hidden" data-name="timeStarted" value="1437732000" />
                  <input type="hidden" data-name="timeEnded" value="1437818400" />
                                    <span class="days hide">0</span>
                  <span class="d hide">d</span>
                  <span class="hours">15</span>
                  <span class="h">h</span>
                  <span class="minutes">42</span>
                  <span class="m">m</span>
                  <span class="seconds">56</span>
                  <span class="m">s</span>
               </div>
            </div>
         </div>
         <div class="lottery-foot-wrap ">
            <div class="tc-info bold">
               <i class="tc"> <i class="tc-hl"></i> </i>
               <span class="totalTickets">0</span> tickets bought
            </div>
            <div class="desc">
               Ticket for the
               <span class="bold">Daily Dime</span>
               are
               <span class="bold">$100</span>
               and
               <span class="bold">one casino token</span>
               each.
            </div>
            <div class="act-wrap bold">
                              <div class="btn-wrap silver ">
                  <div href="loader.php?sid=lotteryPlay&step=buyTicket&lotteryID=1" class="btn" data-ticket-price=100>
                     BUY
                  </div>
               </div>
               <div class="tc-price">
                  <div>
                     Ticket Price: $100 + 1 CT
                  </div>
               </div>
               <div class="clear"></div>
            </div>
         </div>
      </div>
              
</div>
</table>
<?php
include 'footer.php';
?>