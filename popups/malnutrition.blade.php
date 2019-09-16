<?php

class Malnutrition_Blade_Popup extends DB{
    private $data;
        
    function __construct() {
        // check db if there is any occurance\
        parent::__construct(); 
        $this->table = "tbl_client_malnutrition";
        $this->isNew = true;
        $this->series = 1;
        $this->visit_no = 1;
    }
    public function filter_and_save($data) {
        $_data['series'] = $data['series'];
        $_data['hiv_status'] = $data['hiv_status'];
        $_data['tb_diagnosed'] = $data['tb_diagnosed'];
        $_data['muac'] = $data['muac'];
        $_data['oedema'] = $data['oedema'];
        $_data['wfh'] = $data['wfh'];
        $_data['client_id'] = $data['client_id'];
        $_data['reason'] = $data['reason'];
        return $this->save($_data, array(), $this->table, 'lastInsertId');
    }
    public function remove($id) {
        $this->delete($id);
    }
    public function update($data=null, $ID=null, $CLIENT_ID=null) { // this fix was only for php7 compaitbility
        $data = array(
            'tb_diagnosed' => $_POST['tb_diagnosed'],
            'hiv_status' => $_POST['hiv_status'],
            'muac' => $_POST['muac'],
            'wfh' => $_POST['wfh'],
            'oedema' => $_POST['oedema'],
            'reason' => $_POST['reason']
        );
        $this->save($data, array('id' => $_POST['id']));
        exit();
    }
    public function markAsNOTPrevious($malnutId) {
        $this->save(array('isPrevious'=>0), array('id'=>$malnutId));
    }
    public function markAsPrevious($malnutId) {
        $this->save(array('isPrevious'=>1), array('id'=>$malnutId));
    }
    public function getVisitCount($id) {
        $query = "SELECT a.* FROM tbl_records a, tbl_client_malnutrition b 
                WHERE a.`client_malnutrition_id`= :id AND b.id = :id AND b.isPrevious = 0";
        $bind_array = array("id"=>$id);

        $stmt = $this->query($query,$bind_array);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return count($data)+1;
    }
    public function getData() {


        $malnut = $this->select("*",array("client_id"=>$_GET['cid']), false, $this->table, true, "id", 'DESC');
        if($malnut!=false) {
            $this->isNew = false;
            $this->data = $malnut[0];


            $visit = $this->select("*",array("client_id"=>$_GET['cid']), false, $this->table, true, "id", 'DESC');

            $this->visit_no = $this->getVisitCount($this->data['id']);
            if($malnut[0]['isPrevious']==1) {
                $this->data['series'] = $this->data['series'] + 1;
                $this->isNew = true;
            }
            $this->series = $this->data['series'];
        }
    }
    private function render_readonlyfield($arg = []) {
        ?>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label><?php echo $arg['title'] ?></label><span class="required_field">*</span>
                <input id="<?php echo $arg['slug'] ?>" class="form-control" readonly/>
            </div>
        </div>
        <?php
    }
    private function render_selectfield($arg = array()) { // title, slug, options
        ?>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label><?php echo $arg['title'] ?></label><span class="required_field">*</span>
                <input class="form-control fornoneditable" type="text" placeholder="<?php echo $this->data[ $arg['slug'] ] ?>" readonly/>
                <select class="form-control foreditable required_when_able" id="<?php echo $arg['slug'] ?>" name="<?php echo $arg['slug'] ?>">
                    <option value="">Select <?php echo $arg['title'] ?></option>
                    <?php 
                    foreach($arg['options'] as $key=>$val) {
                        ?><option value="<?php echo $val ?>"><?php echo $val ?></option><?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <?php
    }
    public function render_readonly() {
        ?>
        <div class="consultation-sub-block" style="display: block;">
            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <h3>Malnutrition (MAM/SAM)</h3>
                    <div class="malnutinitinfo noneditable"> 
                        <h5 class="fornoneditable">Enrollment #<span id="series_no"><?php echo $this->series ?></span></h5>
                    </div>
                </div>
            </div>
            <div class="malnutinitinfo noneditable">
                <div class="row">
                    <?php
                    $this->render_readonlyfield(['title'=>'Reason', 'slug'=>'reason']);
                    $this->render_readonlyfield(['title'=>'HIV Status', 'slug'=>'hiv_status']);
                    ?>
                </div>
                <div class="row">
                    <?php
                    $this->render_readonlyfield(['title'=>'TB Diagnosed', 'slug'=>'tb_diagnosed']);
                    $this->render_readonlyfield(['title'=>'MUAC < 11.5cm', 'slug'=>'muac']);
                    ?>
                </div>
                <div class="row">
                    <?php
                    $this->render_readonlyfield(['title'=>'Oedema', 'slug'=>'oedema']);
                    $this->render_readonlyfield(['title'=>'WFH = or < -3 SD', 'slug'=>'wfh']);
                    ?>
                </div>
            </div>
            <hr />
            <div class="row">
                <?php
                    $this->render_readonlyfield(['title'=>'No. of RUTF given', 'slug'=>'rutf']);
                    $this->render_readonlyfield(['title'=>'Referral to Hospital', 'slug'=>'ref_hospital']);
                ?>
            </div>
            <hr />
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group review_date_future_readonly">
                        <label>Review Date (in future)</label><span class="required_field">*</span>
                        <input type="text" id="review_date_future" class="form-control" readonly>
                    </div>
                    <div class="form-group outcome_review_readonly">
                        <label>Outcome of Consultation</label><span class="required_field">*</span>
                        <input type="text" id="outcome_review" class="form-control" value="n/a" readonly>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    public function render() {
        $this->getData();
        ?>
        <?php if(!$this->isNew) : ?>
            <input type="hidden" id="client_malnutrition_id" name="client_malnutrition_id" value="<?php echo $this->data['id'] ?>" />
        <?php endif; ?>
        <input type="hidden" name="series" value="<?php echo $this->series ?>" />
        <div id="malnutgroup" class="consultation-sub-block row" style="display:none">
            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <?php if(!$this->isNew) : ?>
                        <button class="btn btn-secondary" id="btn-edit-malnutrition-info">Edit Malnutrition Info</button>
                    <?php endif; ?>
                    <h3>Malnutrition (MAM/SAM)</h3>
                    <div class="malnutinitinfo <?php echo (!$this->isNew) ? 'noneditable' : ''; ?>"> 
                        <h5 class="fornoneditable">Enrollment #<span id="series_no"><?php echo $this->series ?></span> - Visit #<span id="visit_no"><?php echo $this->visit_no ?></span></h5>
                    </div>
                </div>
            </div>
            <div class="malnutinitinfo <?php echo (!$this->isNew) ? 'noneditable' : 'editable'; ?>">
                <div class="row">
                    <?php 
                    $this->render_selectfield( ['title'=> 'Reason', 'slug'=>'reason', 'options' => [
                        'New Enrollment', 'Defaulter', 'Non respondent', 'Relapse'
                    ]] );
                    
                    $this->render_selectfield( ['title'=> 'HIV Status', 'slug'=>'hiv_status', 'options' => [
                        'Positive', 'Negative', 'Unknown'
                    ]] );
                    ?>
                </div>
                <div class="row">
                    <?php
                    $this->render_selectfield( ['title'=> 'TB Diagnosed', 'slug'=>'tb_diagnosed', 'options' => [
                        'Yes', 'No', 'Unknown'
                    ]] );
                    $this->render_selectfield( ['title'=> 'MUAC < 11.5cm', 'slug'=>'muac', 'options' => [
                        'Yes', 'No'
                    ]] );
                    ?>
                </div>
                <div class="row">
                    <?php
                    $this->render_selectfield( ['title'=> 'Oedema', 'slug'=>'oedema', 'options' => [
                        '0', '+', '++', '+++'
                    ]] );
                    $this->render_selectfield( ['title'=> 'WFH = or < -3 SD', 'slug'=>'wfh', 'options' => [
                        'Yes', 'No'
                    ]] );
                    ?>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                    <label>No. of RUTF given </label><span class="required_field">*</span>
                    <input id="rutf" name="rutf" type="number" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control required_when_able" value="-">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label>Referral to Hospital </label><span class="required_field">*</span>
                        <select class="form-control required_when_able" id="ref_hospital" name="ref_hospital">
                            <option value="">Select Referral to Hospital</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="is_final_consultation" id="is_final_consultation" value="Yes" />  Tick this if final consultation.
                    </label>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-xs-12 col-sm-6 notfinalconsultation">
                    <div class="form-group">
                    <label>Review Date (in future)</label><span class="required_field">*</span>
                    <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control required_when_able" id="datepicker-malnu-review_date" name="review_date_future" placeholder="Enter Review Date" >
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 finalconsultation" style="display:none;">
                    <div class="form-group">
                    <label>Outcome of Consultation</label><span class="required_field">*</span>
                    <select class="form-control required_when_able" id="outcome_review" name="outcome_review">
                        <option value="">Select Outcome of Consultation</option>
                        <option value="Discharged">Discharged</option>
                        <option value="Death">Death</option>
                        <option value="Defaulter">Defaulter</option>
                        <option value="Non Respondent">Non Respondent</option>
                    </select>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $this->script();
    }
    function script() {
        ?>
        <script>
            $(document).ready(function() {
                $('#is_final_consultation').on('change', function() {
                    $('.finalconsultation').toggle(this.checked)
                    $('.notfinalconsultation').toggle(!this.checked)

                    $('.required_when_able').not(':focusable').prop('required', false);
                    $('.required_when_able:focusable').prop('required', true);

                })
                $('#oedema').on('change',function() {
                    if($(this).val()!=='0') {
                        $('#ref_hospital').val('Yes');                       
                    }
                    else {
                        $('#ref_hospital').val('');   
                    }
                })
                $('#btn-edit-malnutrition-info').on('click', function(e) {
                    e.preventDefault();
                    let button = this;
                    $('.required_when_able').not(':focusable').prop('required', false);
                    $('.required_when_able:focusable').prop('required', true);
                    const saveText = "Save Now";
                    $('.malnutinitinfo').toggleClass(function() {
                        if($(this).hasClass('editable')) {
                            $(this).removeClass('editable');
                            $(button).html('Edit Malnutrition Info');
                            
                            const data = {
                                tb_diagnosed : $('#tb_diagnosed').val(),
                                hiv_status : $('#hiv_status').val(),
                                muac : $('#muac').val(),
                                oedema : $('#oedema').val(),
                                wfh : $('#wfh').val(),
                                id : $('#client_malnutrition_id').val(),
                                class : 'Malnutrition_Blade_Popup',
                                func : 'update'
                            }   

                            $.post(window.location.href,data, function(ret){
                                $('#tb_diagnosed').prev().val( data.tb_diagnosed ).attr('placeholder', data.tb_diagnosed);
                                $('#hiv_status').prev().val( data.hiv_status ).attr('placeholder', data.hiv_status);
                                $('#muac').prev().val( data.muac ).attr('placeholder', data.muac);
                                $('#oedema').prev().val( data.oedema ).attr('placeholder', data.oedema);
                                $('#wfh').prev().val( data.wfh ).attr('placeholder', data.wfh);
                            });     
                            
                            return 'noneditable';
                        }
                        else {
                            $(this).removeClass('noneditable');
                            $('#tb_diagnosed').val( $('#tb_diagnosed').prev().attr('placeholder') );
                            $('#hiv_status').val( $('#hiv_status').prev().attr('placeholder') );
                            $('#muac').val( $('#muac').prev().attr('placeholder') );
                            $('#oedema').val( $('#oedema').prev().attr('placeholder') );
                            $('#wfh').val( $('#wfh').prev().attr('placeholder') );
                            $(button).html(saveText);
                            
                            return 'editable';
                        }
                    });
                })
                $('#new-enrollment').on('click',function(e) {
                    e.preventDefault();
                    let x = confirm('Are you sure this is a new enrollment?');
                    if(x) {
                        $('#series_no').val( parseInt($('#series_no').val()) + 1 )
                    }
                })

            });
        </script>
        <?php
    }
}
