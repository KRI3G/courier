<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier</title>
</head>
<body onselectstart="return false">
  <!-- Stolen from  https://github.com/szimek/signature_pad
        Thanks, Szymon! :D
        (c) 2023 Szymon Nowak | Released under the MIT license-->
  <span id="forkongithub">
    <a href="https://github.com/szimek/signature_pad">Fork me on GitHub</a>
  </span>

  <div id="signature-pad" class="signature-pad">
    <div id="canvas-wrapper" class="signature-pad--body">
      <canvas style="touch-action: none; user-select: none;" width="664" height="292"></canvas>
    </div>
    <div class="signature-pad--footer">
      <div class="description">Sign above</div>

      <div class="signature-pad--actions">
        <div class="column">
          <button type="button" class="button clear" data-action="clear">Clear</button>
          <button type="button" class="button" data-action="undo" title="Ctrl-Z">Undo</button>
          <button type="button" class="button" data-action="redo" title="Ctrl-Y">Redo</button>
          <br>
          <button type="button" class="button" data-action="change-color">Change color</button>
          <button type="button" class="button" data-action="change-width">Change width</button>
          <button type="button" class="button" data-action="change-background-color">Change background color</button>

        </div>
        <div class="column">
          <button type="button" class="button save" data-action="save-png">Save as PNG</button>
          <button type="button" class="button save" data-action="save-jpg">Save as JPG</button>
          <button type="button" class="button save" data-action="save-svg">Save as SVG</button>
          <button type="button" class="button save" data-action="save-svg-with-background">Save as SVG with
            background</button>
        </div>
      </div>

      <div>
        <button type="button" class="button" data-action="open-in-window">Open in Window</button>
      </div>
    </div>
  </div>

  <script src="js/signature_pad.umd.min.js"></script>
  <script src="js/app.js"></script>



</body>
</html>