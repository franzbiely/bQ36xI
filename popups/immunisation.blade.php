<?php
class Immunisation_Blade_Popup extends DB
{
    private $data;
        
    public function __construct()
    {
        // check db if there is any occurance\
        parent::__construct();
        $this->table = "tbl_client_immunisation";
        $this->isNew = true;
        $this->series = 1;
        $this->visit_no = 1;
    }
    public function save_immunisation($data, $im_type)
    {
        $_data['client_id'] = $data['client_id'];
        $_data['record_id'] = $data['record_id'];
        $_data['type'] = $im_type;
        return $this->save($_data, array(), $this->table_im, 'lastInsertId');
    }
    public function render()
    {
    ?>
    <input type="hidden" name="series" value="<?php echo $this->series ?>" />
    <div id="immunisationgroup" class="consultation-sub-block row" style="display:none">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h3>Immunisation Details</h3>
            </div>
        </div>
        <div class="immunisationinitinfo <?php echo (!$this->isNew) ? 'noneditable' : 'editable'; ?>">
            <div class="row">
                <?php
                $this->render_checkfield(['title'=> 'Immunisation Type', 'slug'=>'immunisation_type', 'options' => [
                    '1st dose of Pentavalent', '3rd dose of Pentavalent', '3rd dose of bOPV (sabin)', 'IPV', 'Measles Rubella (MR)',
                    '3rd dose of PCV3','BCG','HepB','2nd Dose+ of Tetanus Toxoid'
                ]]); ?>
            </div>
        </div>
    </div>
    <?php
    }
    private function render_checkfield($arg = array()) { // title, slug, options
        ?>
        <div class="col-xs-12 col-sm-12">
            <div class="form-group">
                <label><?php echo $arg['title'] ?></label><span class="required_field">*</span>
                <br />
                <?php foreach($arg['options'] as $key=>$val) { ?>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="<?php echo $arg['slug'] ?>[]" id="<?php echo $arg['slug'] ?>" value="<?php echo $val ?>" multiple="">  
                        <?php echo $val ?>
                    </label>
                <?php } ?>
                </select>
            </div>
        </div>
        <?php
    }
}
?>