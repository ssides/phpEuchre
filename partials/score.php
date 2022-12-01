      <table>
      <tr><td><span style="display:none" data-bind="text: label, visible: showScoreGroup"></span><span style="display:none" data-bind="visible: showScoreGroup">:&nbsp;</span><span style="display:none" data-bind="text: score, visible: showScoreGroup"></span></td><td></td></tr>
      <tr><td><div class="tricksBorder" data-bind="html: tricks"></div></td><td>      
      <div class="trump" data-bind="visible: trumpURL().length > 0">
        <img  data-bind="attr: {src: trumpURL }" style="width: 14px; height: 14px; margin-left: 4px;"  />
      </div></td></tr>
      </table>