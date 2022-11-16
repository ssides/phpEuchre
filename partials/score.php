      <table>
      <tr><td><span data-bind="text: label"></span>:&nbsp;<span data-bind="text: score"></span></td><td></td></tr>
      <tr><td><div class="tricksBorder" data-bind="html: tricks"></div></td><td>      
      <div class="trump" data-bind="visible: trumpURL().length > 0">
        <img  data-bind="attr: {src: trumpURL }" style="width: 14px; height: 14px; margin-left: 4px;"  />
      </div></td></tr>
      </table>