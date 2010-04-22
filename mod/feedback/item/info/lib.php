<?php
defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_info extends feedback_item_base {
    var $type = "info";
    function init() {

    }

    function show_edit($item, $commonparams, $positionlist, $position) {
        global $CFG;

        require_once('info_form.php');

        $item_form = new feedback_info_form('edit_item.php', array('item'=>$item, 'common'=>$commonparams, 'positionlist'=>$positionlist, 'position'=>$position));

        $item->presentation = empty($item->presentation) ? '' : $item->presentation;
        $item->name = empty($item->name) ? '' : htmlspecialchars($item->name);
        $item->label = empty($item->label) ? '' : $item->label;

        $item_form->requiredcheck->setValue(false);

        $item_form->itemname->setValue($item->name);
        $item_form->itemlabel->setValue($item->label);

        $item_form->infotype->setValue($item->presentation);

        return $item_form;
    }

    //liefert eine Struktur ->name, ->data = array(mit Antworten)
    function get_analysed($item, $groupid = false, $courseid = false) {

        $presentation = $item->presentation;
        $aVal = null;
        $aVal->data = null;
        $aVal->name = $item->name;
        //$values = get_records('feedback_value', 'item', $item->id);
        $values = feedback_get_group_values($item, $groupid, $courseid);
        if($values) {
            $data = array();
            $datavalue = new object();
            foreach($values as $value) {

                switch($presentation) {
                    case 1:
                        $datavalue->value = $value->value;
                        $datavalue->show = UserDate($datavalue->value);
                        break;
                    case 2:
                        $datavalue->value = $value->value;
                        $datavalue->show = $datavalue->value;
                        break;
                    case 3:
                        $datavalue->value = $value->value;
                        $datavalue->show = $datavalue->value;
                        break;
                }

                $data[] = $datavalue;
            }
            $aVal->data = $data;
        }
        return $aVal;
    }

    function get_printval($item, $value) {

        if(!isset($value->value)) return '';
        return UserDate($value->value);
    }

    function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {
        $analysed_item = $this->get_analysed($item, $groupid, $courseid);
        $data = $analysed_item->data;
        if(is_array($data)) {
            echo '<tr><th colspan="2" align="left">'. $itemnr . '&nbsp;('. $item->label .') ' . $item->name .'</th></tr>';
            for($i = 0; $i < sizeof($data); $i++) {
                echo '<tr><td colspan="2" valign="top" align="left">-&nbsp;&nbsp;' . str_replace("\n", '<br />', $data[$i]->show) . '</td></tr>';
            }
        }
        // return $itemnr;
    }

    function excelprint_item(&$worksheet, $rowOffset, $xlsFormats, $item, $groupid, $courseid = false) {
        $analysed_item = $this->get_analysed($item, $groupid, $courseid);

        // $worksheet->setFormat("<l><f><ro2><vo><c:green>");
        $worksheet->write_string($rowOffset, 0, $item->label, $xlsFormats->head2);
        $worksheet->write_string($rowOffset, 1, $item->name, $xlsFormats->head2);
        $data = $analysed_item->data;
        if(is_array($data)) {
            // $worksheet->setFormat("<l><ro2><vo>");
            $worksheet->write_string($rowOffset, 2, $data[0]->show, $xlsFormats->value_bold);
            $rowOffset++;
            for($i = 1; $i < sizeof($data); $i++) {
                // $worksheet->setFormat("<l><vo>");
                $worksheet->write_string($rowOffset, 2, $data[$i]->show, $xlsFormats->default);
                $rowOffset++;
            }
        }
        $rowOffset++;
        return $rowOffset;
    }
    
    /**     
     * print the item at the edit-page of feedback
     *
     * @global object
     * @param object $item
     * @return void
     */
    function print_item_preview($item) {
        global $USER, $DB, $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';

        $presentation = $item->presentation;
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        ?>
        <td valign="top" align="<?php echo $align;?>">
        <?php
            echo '('.$item->label.') ';
            echo format_text($item->name . $requiredmark, true, false, false);
        ?>
        </td>
        <td valign="top" align="<?php echo $align;?>">
        <?php
            $feedback = $DB->get_record('feedback', array('id'=>$item->feedback));
            $course = $DB->get_record('course', array('id'=>$feedback->course));
            $coursecategory = $DB->get_record('course_categories', array('id'=>$course->category));
            switch($presentation) {
                case 1:
                    $itemvalue = time();
                    $itemshowvalue = UserDate($itemvalue);
                    break;
                case 2:
                    $itemvalue = $course->shortname;
                    $itemshowvalue = $itemvalue;
                    break;
                case 3:
                    $itemvalue = $coursecategory->name;
                    $itemshowvalue = $itemvalue;
                    break;
            }
        ?>
            <input type="hidden" name="<?php echo $item->typ . '_' . $item->id;?>"
                                    value="<?php echo $itemvalue;?>" />
            <span><?php echo $itemshowvalue;?></span>
        </td>
        <?php
    }
    
    /**     
     * print the item at the complete-page of feedback
     *
     * @global object
     * @param object $item
     * @param string $value
     * @param bool $highlightrequire
     * @return void
     */
    function print_item_complete($item, $value = '', $highlightrequire = false) {
        global $USER, $DB, $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';

        $presentation = $item->presentation;
        if($highlightrequire AND $item->required AND strval($value) == '') {
            $highlight = 'bgcolor="#FFAAAA" class="missingrequire"';
        }else {
            $highlight = '';
        }
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
    ?>
        <td <?php echo $highlight;?> valign="top" align="<?php echo $align;?>">
        <?php
            echo format_text($item->name . $requiredmark, true, false, false);
        ?>
        </td>
        <td valign="top" align="<?php echo $align;?>">
    <?php
            $feedback = $DB->get_record('feedback', array('id'=>$item->feedback));
            $course = $DB->get_record('course', array('id'=>$feedback->course));
            $coursecategory = $DB->get_record('course_categories', array('id'=>$course->category));
            switch($presentation) {
                case 1:
                    $itemvalue = time();
                    $itemshowvalue = UserDate($itemvalue);
                    break;
                case 2:
                    $itemvalue = $course->shortname;
                    $itemshowvalue = $itemvalue;
                    break;
                case 3:
                    $itemvalue = $coursecategory->name;
                    $itemshowvalue = $itemvalue;
                    break;
            }
    ?>
            <input type="hidden" name="<?php echo $item->typ . '_' . $item->id;?>"
                                    value="<?php echo $itemvalue;?>" />
            <span><?php echo $itemshowvalue;?></span>
        </td>
    <?php
    }

    /**     
     * print the item at the complete-page of feedback
     *
     * @global object
     * @param object $item
     * @param string $value
     * @return void
     */
    function print_item_show_value($item, $value = '') {
        global $USER, $DB, $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';

        $presentation = $item->presentation;
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
    ?>
        <td valign="top" align="<?php echo $align;?>">
        <?php
            echo '('.$item->label.') ';
            echo format_text($item->name . $requiredmark, true, false, false);
        ?>
        </td>
        <td valign="top" align="<?php echo $align;?>">
    <?php
            echo $OUTPUT->box_start('generalbox boxalign'.$align);
            echo $value ? UserDate($value):'&nbsp;';
            echo $OUTPUT->box_end();
    ?>
        </td>
    <?php
    }

    function check_value($value, $item) {
        return true;
    }

    function create_value($data) {
        $data = addslashes(clean_text($data));
        return $data;
    }

    function get_presentation($data) {
        return $data->infotype;
    }

    function get_hasvalue() {
        return 1;
    }
}
?>
