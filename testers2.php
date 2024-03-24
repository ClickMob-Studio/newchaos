<!DOCTYPE HTML>
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
      <title>Stat / Exp Calculator</title>
      <style type="text/css">
         body {font-family:tahoma;}
         .bor td{border-style:solid;border-width:thin;border-color:#FF006F;}
         .bor table td{border-width:0px}
         input[type=submit]{color: #FF006F;  background-color: black; border-color: #FF006F}
         input[type=number]{color: #FF006F;  background-color: black; border-color: #FF006F}
      </style>
   </head>
   <body onload="check()" style="background-color:#000;color:#FF006F;">
      <center>
         <table class="bor">
            <tbody>
               <tr>
                  <td>
                     <center>What Level?</center>
                  </td>
                  <td>
                     <center>How Many Suicides?</center>
                  </td>
                  <td>
                     <center>Exp Calc</center>
                  </td>
               </tr>
               <tr>
                  <td>
                     <form action="">
                        <table>
                           <tbody>
                              <tr>
                                 <td># of Suicides:</td>
                                 <td> <input id="Suicides" pattern="[0-9.]+" maxlength="6" type="number"></td>
                              </tr>
                              <tr>
                                 <td>Customers's Level:</td>
                                 <td> <input id="clevel" pattern="[0-9.]+" maxlength="4" type="number"></td>
                              </tr>
                              <tr>
                                 <td>Bunny's Level:</td>
                                 <td> <input id="blevel" pattern="[0-9.]+" maxlength="4" type="number"></td>
                              </tr>
                              <tr>
                                 <td colspan="2">
                                    <center><input value="submit" onclick="whatLevel()" type="submit"></center>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </form>
                  </td>
                  <td>
                     <form action="">
                        <table>
                           <tbody>
                              <tr>
                                 <td>Customer's Starting Level:</td>
                                 <td> <input id="clevela" pattern="[0-9.]+" maxlength="4" type="number"></td>
                              </tr>
                              <tr>
                                 <td>Customer's Desired Level:</td>
                                 <td> <input id="flevela" pattern="[0-9.]+" maxlength="4" type="number"></td>
                              </tr>
                              <tr>
                                 <td>Bunny's Level:</td>
                                 <td> <input id="blevela" pattern="[0-9.]+" maxlength="4" type="number"></td>
                              </tr>
                              <tr>
                                 <td colspan="2">
                                    <center><input value="submit" onclick="howManySuicides()" type="submit"></center>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </form>
                  </td>
                  <td>
                     <form action="">
                        <table>
                           <tbody>
                              <tr>
                                 <td>Starting Level:</td>
                                 <td> <input id="levelfrom" pattern="[0-9.]+" maxlength="4" type="number"></td>
                              </tr>
                              <tr>
                                 <td>Finished Level:</td>
                                 <td> <input id="levelto" pattern="[0-9.]+" maxlength="4" type="number"></td>
                              </tr>
                              <tr>
                                 <td colspan="2">
                                    <center><input value="submit" onclick="expCalc()" type="submit"></center>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </form>
                  </td>
               </tr>
            </tbody>
         </table>
         <script type="text/javascript">
            function whatLevel(){
                var Suicides = (parseInt(document.getElementById('Suicides').value));
                var clevel = (parseInt(document.getElementById('clevel').value));
                var blevel = (parseInt(document.getElementById('blevel').value));
                var slevel = (parseInt(clevel));
                var sSuicides = (parseInt(Suicides));
                if(!Suicides || !clevel || !blevel || !slevel || !sSuicides){throw new Error("my error message");}
                var exp = (parseInt(expneeded(clevel)));
                var expg = (parseInt((blevel-clevel)*100));
                if (expg>12000){expg=12000;}
                while(Suicides>0){
                exp=(parseInt(exp-expg));
                Suicides--;
                if(exp<=0){
                clevel++;
                expg=(parseInt((blevel-clevel)*100));
                if (expg>12000){expg=12000;}
                exp=(parseInt(expneeded(clevel)+exp));
                }
                }
                window.sessionStorage['clevel'] = clevel;
                window.sessionStorage['Suicides'] = sSuicides;
                window.sessionStorage['slevel'] = slevel;
                window.sessionStorage['blevel'] = blevel;
                window.sessionStorage['what'] = "whatLevel";
            }
            
            function howManySuicides(){
            var flevel = (parseInt(document.getElementById('flevela').value));
            var clevel = (parseInt(document.getElementById('clevela').value));
            var blevel = (parseInt(document.getElementById('blevela').value));
            var slevel = (parseInt(clevel));
                if(!clevel || !blevel || !slevel || !flevel){throw new Error("my error message");}
            var exp = (parseInt(expneeded(clevel)));
            var Suicides = (parseInt(0));
            var expg = (parseInt((blevel-clevel)*100));
            if (expg>12000){expg=12000;}
            while(clevel<flevel){
            exp=(parseInt(exp-expg));
            Suicides++;
            if(exp<=0){
            clevel++;
            expg=(blevel-clevel)*100;
            if (expg>12000){expg=12000;}
            exp=(parseInt(expneeded(clevel)+exp));
            }
            }
            
                window.sessionStorage['Suicides'] = Suicides;
                window.sessionStorage['flevel'] = flevel;
                window.sessionStorage['slevel'] = slevel;
                window.sessionStorage['blevel'] = blevel;
                window.sessionStorage['what'] = "howManySuicides";
            }
            
            function expCalc(){
            var i = (parseInt(document.getElementById('levelfrom').value));
            var f = (parseInt(document.getElementById('levelto').value));
            var slevel = (parseInt(i));
                if(!f || !i || !slevel){throw new Error("my error message");}
            var m = (parseInt(0));
            while(i<f){
            m=(parseInt(m+(expneeded(i))));
            i++;
            }
            
                window.sessionStorage['slevel'] = slevel;
                window.sessionStorage['flevel'] = f;
                window.sessionStorage['exp'] = m;
                window.sessionStorage['what'] = "expCalc";
            }
            function check(){
                if(window.sessionStorage['what'] == "whatLevel"){
                    alert('With ' + format_number(window.sessionStorage['Suicides']) + ' Suicides\nstarting at level ' + format_number(window.sessionStorage['slevel']) + '\nwill reach level ' + format_number(window.sessionStorage['clevel']) + '\nwith a level ' + format_number(window.sessionStorage['blevel']) + ' bunny.');
                    window.sessionStorage['what'] = '';
                }
                if(window.sessionStorage['what'] == "howManySuicides"){
                    alert('It takes ' + format_number(window.sessionStorage['Suicides']) + ' Suicides\nto get a level ' + format_number(window.sessionStorage['slevel']) + '\nto level ' + format_number(window.sessionStorage['flevel']) + '\nwith a level ' + format_number(window.sessionStorage['blevel']) + ' bunny.');
                    window.sessionStorage['what'] = '';
                }
                if(window.sessionStorage['what'] == "expCalc"){
                    alert('To go from level ' + format_number(window.sessionStorage['slevel']) + ' \nto level ' + format_number(window.sessionStorage['flevel']) + '\nyou need ' + format_number(window.sessionStorage['exp']) + ' Exp');
                    window.sessionStorage['what'] = '';
                }
            }
            function expneeded(level){
                var a = 0;
                var end = 0;
                for (var x = 1; x < level; x++)
                    a += Math.ceil(x + 1500 * Math.pow(4, (x / 190)));
                if (x >= 200)
                    a *= 2;
                if (level >= 300)
                    a *= 2;
                if (level >= 400)
                    a *= 2;
                if (level >= 500)
                    a *= 2;
                if (level >= 600)
                    a *= 2;
                if (level >= 700)
                    a *= 2;
                if (level >= 800)
                    a *= 2;
                if (level >= 900)
                    a *= 2;
                if (level >= 1000)
                    a *= 2;
                return Math.ceil(a / 4);
            }
            function format_number(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
         </script>
      </center>
   </body>
</html>

