<div class="info">
  <table>
    <tr>
      <td style="vertical-align: middle;">
        <div data-bind="visible: thumbnailURL().length > 0">
          <img data-bind="attr: {src: thumbnailURL() }" style="height: 15px; width: 15px;" />
        </div>
      </td>
      <td class="playerName">&nbsp;<span data-bind="text: name"></span></td>
      <td>
        <div data-bind="visible: dealer() != ' '">
          <span class="dealer" data-bind="text: dealer"></span>
        </div>
      </td>
      <td>
        <div data-bind="visible: trumpURL().length > 0">
          <img data-bind="attr: {src: trumpURL() }" style="height: 15px; width: 15px;" />
        </div>
      </td>
      <td style="display:none" class="lead" data-bind="visible: showLead">
        <span>Lead</span>
      </td>
    </tr>
    <tr>
      <td colspan=5>
        <div data-bind="visible: iamSkipped() === false">
          <ul class="list-group list-group-horizontal" data-bind="foreach: sortedCards">
            <li class="list-group-item p-0" style="height:29px; width: 29px"  >
              <div class="cardSelectionContainer">
                <div style="margin: 0px; padding: 0px;" class="cardContainer" >
                  <img class="clipCard" data-bind="attr: {src: url}, class: isPlayable() ? '' : 'cardNotPlayable'" />
                </div>
              </div>
            </li>
          </ul>
        </div>
        <span data-bind="visible: iamSkipped() == true">Your partner is taking the hand alone.</span>
      </td>
    </tr>
  </table>
</div>
