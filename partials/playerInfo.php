<div class="info" data-bind="class: isPlayersTurn() ? 'infoTurnBorder' : isPlayerSkipped() ? 'infoSkippedBorder' : 'infoBorder'">
  <table>
    <tr>
      <td style="vertical-align: middle;">
        <div data-bind="visible: thumbnailURL().length > 0">
          <img data-bind="attr: {src: thumbnailURL() }" style="height: 15px; width: 15px;" />
        </div>
      </td>
      <td style="vertical-align: middle;">&nbsp;<span data-bind="text: name"></span></td>
      <td style="vertical-align: middle;">
        <div class="dealerBorder" data-bind="visible: dealer() != ' '">
          &nbsp;<span class="dealer" data-bind="text: dealer"></span>
        </div>
      </td>
      <td>
        <div data-bind="visible: trumpURL().length > 0">
          <img data-bind="attr: {src: trumpURL() }" style="height: 15px; width: 15px;" />
        </div>
      </td>
    </tr>
  </table>
</div>
