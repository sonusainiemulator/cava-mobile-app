<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-heading">
            <h4><?php echo htmlspecialchars($title); ?></h4>
          </div>
          <div class="panel-body">

            <?php echo form_open_multipart(admin_url('escaneo/save_bebida'), ['id' => 'bebida-form']); ?>
              <input type="hidden" name="id" value="<?php echo isset($bebida['id']) ? (int)$bebida['id'] : ''; ?>">
              <input type="hidden" name="barcode_svg" id="barcode_svg" value="">

              <div class="row">
                <div class="col-md-4">
                  <label for="codigo_barras"><?php echo _l('escaneo_codigo_barras'); ?> (EAN-13)</label>
                  <input type="text" name="codigo_barras" id="codigo_barras" class="form-control" value="<?php echo isset($bebida['codigo_barras']) ? htmlspecialchars($bebida['codigo_barras']) : ''; ?>" required>
                  <small class="text-muted"><?php echo _l('escaneo_ean_hint'); ?></small>
                </div>
                <div class="col-md-8">
                  <label><?php echo _l('escaneo_barcode_preview'); ?></label>
                  <div style="border:1px solid #ddd;padding:10px;border-radius:6px;">
                    <div id="barcode_number" style="font-weight:700;margin-bottom:8px;"></div>
                    <svg id="barcode_preview"></svg>
                  </div>
                </div>
              </div>

              <div class="row mtop15">
                <div class="col-md-6">
                  <label><?php echo _l('escaneo_imagen'); ?></label>
                  <input type="file" name="imagen" class="form-control" accept="image/*">
                  <small class="text-muted"><?php echo _l('escaneo_imagen_hint'); ?></small>
                </div>
                <div class="col-md-6">
                  <label><?php echo _l('escaneo_imagen_actual'); ?></label><br>
                  <?php if (!empty($bebida['imagen'])) { 
                    $url = base_url('uploads/escaneo/bebidas/' . $bebida['imagen']);
                  ?>
                    <img src="<?php echo $url; ?>" style="max-width:180px;max-height:180px;border-radius:8px;border:1px solid #ddd;" alt="img">
                  <?php } else { ?>
                    <span class="text-muted"><?php echo _l('escaneo_sin_imagen'); ?></span>
                  <?php } ?>
                </div>
              </div>

              <div class="row mtop15">
                <div class="col-md-4">
                  <label for="marca"><?php echo _l('escaneo_marca'); ?></label>
                  <input type="text" name="marca" id="marca" class="form-control" value="<?php echo isset($bebida['marca']) ? htmlspecialchars($bebida['marca']) : ''; ?>" required>
                </div>
                <div class="col-md-4">
                  <label for="nombre_tequila"><?php echo _l('escaneo_nombre'); ?></label>
                  <input type="text" name="nombre_tequila" id="nombre_tequila" class="form-control" value="<?php echo isset($bebida['nombre_tequila']) ? htmlspecialchars($bebida['nombre_tequila']) : ''; ?>" required>
                </div>
                <div class="col-md-4">
                  <label for="presentacion"><?php echo _l('escaneo_presentacion'); ?></label>
                  <input type="text" name="presentacion" id="presentacion" class="form-control" value="<?php echo isset($bebida['presentacion']) ? htmlspecialchars($bebida['presentacion']) : ''; ?>" required>
                </div>
              </div>

              <div class="row mtop15">
                <div class="col-md-4">
                  <label for="grados_alcohol"><?php echo _l('escaneo_grados_alcohol'); ?></label>
                  <input type="number" step="0.01" name="grados_alcohol" id="grados_alcohol" class="form-control" value="<?php echo isset($bebida['grados_alcohol']) ? htmlspecialchars($bebida['grados_alcohol']) : ''; ?>" required>
                </div>
                <div class="col-md-4">
                  <label for="precio"><?php echo _l('escaneo_precio'); ?></label>
                  <input type="number" step="0.01" name="precio" id="precio" class="form-control" value="<?php echo isset($bebida['precio']) ? htmlspecialchars($bebida['precio']) : ''; ?>" required>
                </div>
              </div>

              <div class="row mtop15">
                <div class="col-md-12">
                  <button type="submit" class="btn btn-primary"><?php echo _l('escaneo_save'); ?></button>
                  <a href="<?php echo admin_url('escaneo/bebidas'); ?>" class="btn btn-default"><?php echo _l('escaneo_back'); ?></a>
                </div>
              </div>
            <?php echo form_close(); ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
(function(){
  const input = document.getElementById('codigo_barras');
  const svg = document.getElementById('barcode_preview');
  const numberBox = document.getElementById('barcode_number');
  const hiddenSvg = document.getElementById('barcode_svg');

  function onlyDigits(s){ return (s||'').replace(/\D+/g,''); }

  function computeEAN13CheckDigit(code12){
    let sum = 0;
    for(let i=0;i<12;i++){
      const d = parseInt(code12.charAt(i),10);
      const pos = i+1;
      sum += (pos % 2 === 0) ? d*3 : d;
    }
    const mod = sum % 10;
    const cd = (mod === 0) ? 0 : (10-mod);
    return String(cd);
  }

  function normalizeEAN13(raw){
    const s = onlyDigits(raw);
    if (s.length === 12) return s + computeEAN13CheckDigit(s);
    if (s.length === 13){
      const base = s.substring(0,12);
      const cd = computeEAN13CheckDigit(base);
      return (cd === s.substring(12,13)) ? s : null;
    }
    return null;
  }

  function render(){
    const normalized = normalizeEAN13(input.value);
    if (!normalized){
      numberBox.textContent = '';
      svg.innerHTML = '';
      hiddenSvg.value = '';
      return;
    }

    numberBox.textContent = normalized;

    JsBarcode(svg, normalized, {
      format: "ean13",
      displayValue: true,
      fontSize: 14,
      margin: 10
    });

    hiddenSvg.value = svg.outerHTML;
  }

  input.addEventListener('input', render);
  document.addEventListener('DOMContentLoaded', render);
  document.getElementById('bebida-form').addEventListener('submit', function(){ render(); });
})();
</script>

<?php init_tail(); ?>
