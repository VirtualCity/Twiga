<?php
/**
 * Created by PhpStorm.
 * User: Bethuel
 * Date: 8/14/14
 * Time: 12:37 AM
 */

//Get View Products action buttons
function get_view_products_buttons($id){
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'products/edit/' . $id . '">&nbsp; Edit</a>';
    $html .= '</span>';

    return $html;
}

function get_active_farmers_buttons($id){
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'farmers/sms/' . $id . '"> SMS</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'farmers/edit/' . $id . '"> Edit</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'farmers/suspend/' . $id . '"> Suspend</a>';

    $html .= '</span>';

    return $html;
}

function get_inactive_farmers_buttons($id){
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'farmers/sms/' . $id . '"> SMS</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'farmers/edit/' . $id . '"> Edit</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'farmers/activate/' . $id . '"> Activate</a>';
    $html .= '</span>';

    return $html;
}

function get_active_stockists_buttons($id){
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'stockists/sms/' . $id . '"> SMS</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'stockists/edit/' . $id . '"> Edit</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'stockists/suspend/' . $id . '"> Suspend</a>';

    $html .= '</span>';

    return $html;
}

function get_inactive_stockists_buttons($id){
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'stockists/sms/' . $id . '"> SMS</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'stockists/edit/' . $id . '"> Edit</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'stockists/activate/' . $id . '"> Activate</a>';
    $html .= '</span>';

    return $html;
}

function get_active_distributors_buttons($id){
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'distributors/deactivate/' . $id . '">&nbsp; Deactivate</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'distributors/edit/' . $id . '">&nbsp; Edit</a>';
    $html .= '</span>';

    return $html;
}

function get_inactive_distributors_buttons($id){
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'distributors/activate/' . $id . '">&nbsp; Activate</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'distributors/edit/' . $id . '">&nbsp; Edit</a>';
    $html .= '</span>';

    return $html;
}


/*View purchases Page*/
function get_view_purchases_buttons($id)
{
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'reports/purchases/products/' . $id . '">&nbsp; View</a>';
    $html .= '</span>';

    return $html;
}

function get_blacklist_buttons($id)
{
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'blacklist/remove/' . $id . '">&nbsp; Re-instate</a>';
    $html .= '</span>';

    return $html;
}


function get_locations_buttons($id)
{
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'locations/edit/' . $id . '">&nbsp; Edit</a>';
    $html .= '</span>';

    return $html;
}

function get_towns_ta_buttons($id,$town)
{
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'towns/removeta/' . $id . '/'.$town.'">&nbsp; Remove</a>';
    $html .= '</span>';

    return $html;
}

function get_towns_buttons($id)
{
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'towns/edit/' . $id . '">&nbsp; Edit</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'towns/tas/' . $id . '">&nbsp; View TAs</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'towns/addtas/' . $id . '">&nbsp; Add TAs</a>';
    $html .= '</span>';

    return $html;
}

function get_regions_buttons($id)
{
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'regions/edit/' . $id . '">&nbsp; Edit</a>';
    $html .= '</span>';

    return $html;
}


function get_groups_buttons($id)
{
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'groups/contacts/' . $id . '">&nbsp; View</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'groups/edit/' . $id . '">&nbsp; Edit</a>';
    $html .= '</span>';

    return $html;
}

function get_area_managers_buttons($id){
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'managers/edit/' . $id . '">&nbsp; Edit</a>';
    $html .= '</span>';

    return $html;
}

function get_tas_buttons($id){
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'tas/edit/' . $id . '">&nbsp; Edit</a>';
    $html .= '</span>';

    return $html;
}

/* ---------------------------------------------------------------------------------------------------------------*/
function get_contacts_buttons($id)
{
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'contacts/sms/' . $id . '"><img src="' . base_url() . 'assets/img/sms.png"/>&nbsp; SMS</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="' . base_url() . 'editcontact/index/' . $id . '"><img src="' . base_url() . 'assets/img/edit.png" />&nbsp; Edit</a>';
    $html .= '</span>';

    return $html;
}

function get_received_messages_buttons($id)
{
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'messages/received/reply/' . $id . '"><img src="' . base_url() . 'assets/img/reply.png"/>&nbsp; Reply</a>';
    $html .= '</span>';

    return $html;
}



function get_active_users_buttons($id)
{
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'users/suspend/' . $id . '"><img src="' . base_url() . 'assets/img/suspend.png"/>&nbsp; Suspend</a>';
    $html .= '&nbsp; | &nbsp;';
    $html .= '<a href="#myModal2" data-toggle="modal" onclick="setUser('.$id.');"><img src="' . base_url() . 'assets/img/reset.png" />&nbsp; Reset</a>';
    $html .= '</span>';

    return $html;
}

function get_suspended_users_buttons($id)
{
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'users/activate/' . $id . '"><img src="' . base_url() . 'assets/img/activate.png"/>&nbsp; Activate</a>';
    $html .= '</span>';

    return $html;
}



function get_location_managers_buttons($id)
{
    $ci = & get_instance();
    $html = '<span class="actions">';
    $html .= '<a href="' . base_url() . 'managers/edit/' . $id . '"><img src="' . base_url() . 'assets/img/edit.png"/>&nbsp; Edit</a>';
    $html .= '</span>';

    return $html;
}