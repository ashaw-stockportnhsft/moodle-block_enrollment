<?php

////// COURSES //////
function quickenrol_get_courses_list($userid) {
    global $DB;
//course_categories is joined and used in this build to restrict enrolment to one parent category. Remove join and clause if not needed.
//Selected all required fields (fullname, dates, visible) for data robustness.
//Added explicit check for active 'manual' enrollment (e_manual.status = 0).    
    $req = "
        SELECT 
            c.id, c.shortname, c.fullname, c.visible, c.startdate, c.enddate
        FROM 
            {course} c
        INNER JOIN 
            {course_categories} cc ON cc.id = c.category
        LEFT JOIN 
            {enrol} e_manual ON e_manual.courseid = c.id AND e_manual.enrol = 'manual'
        LEFT JOIN 
            {enrol} e_user ON e_user.courseid = c.id
        LEFT JOIN 
            {user_enrolments} ue ON ue.enrolid = e_user.id AND ue.userid = :userid
        WHERE
            cc.parent = 25 
            AND c.id != 1
            AND e_manual.status = 0
            AND ue.id IS NULL 
        ORDER BY 
            c.fullname ASC
    ";

    $params = ['userid' => $userid];
    
    $courses = $DB->get_records_sql($req, $params);
    
    // Defensive check: Return an empty array if query failed.
    if ($courses === false) {
        return array();
    }
    
    return $courses;
}

function quickenrol_display_courses_options($userid) {
    $courses = quickenrol_get_courses_list($userid);
    
    // Initialize options and add defensive check.
    $options = ''; 
    if (!is_array($courses) && !is_object($courses)) {
        return $options;
    }
    
    foreach ($courses as $course) {
        // CRITICAL FIX: Use s() and format_string() to safely escape output strings.
        $safe_id = s($course->id);
        $displayname = format_string($course->shortname . ' - ' . $course->fullname);
        
        $options .= '<option value="' . $safe_id . '">' . $displayname . '</option>';
    }
    return $options;
}

////// USERS //////

function quickenrol_display_users_options($currentselecteduser) {
    global $DB;
    $select = "id != 1 AND deleted = 0 AND suspended = 0 AND confirmed = 1 ORDER BY lastname"; 
    $users = $DB->get_recordset_select("user", $select);

    $options = '';
    foreach ($users as $user) {
        $selectedoption = '';
        if ($currentselecteduser == $user->id) {
            $selectedoption = 'selected="selected"';
        }
        
        // Use s() for user data that goes into HTML.
        $lastname = s($user->lastname);
        $firstname = s($user->firstname);
        $email = s($user->email);
        
        $options .= '<option value="' . $user->id . '" ' . $selectedoption . '>' . $lastname . ', ' . $firstname . ' (' . $email . ')' . '</option>'; 
    }
    $users->close();
    return $options;
}

////// ENROL //////
function quickenrol_enrol_user($userid, $courseid, $role, $timestart, $timeend) {
    global $DB, $CFG;
    $instance = $DB->get_record('enrol', array('enrol' => 'manual', 'courseid' => $courseid));
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

    if (!$enrol_manual = enrol_get_plugin('manual')) {
        throw new coding_exception(get_string('nomanenrol', 'block_enrollment'));
    }

    if (!empty($timestart) && !empty($timeend) && $timeend < $timestart) {
        print_error(get_string('dateerror', 'block_enrollment'), null, $CFG->wwwroot . '/blocks/enrollment/enrollment.php');
    }
    if (empty($timestart)) {
        $timestart = $course->startdate;
    }
    if (empty($timeend)) {
        $timeend = 0;
    }
    $enrol_manual->enrol_user($instance, $userid, $role, $timestart, $timeend);
}

////// ROLES //////
function quickenrol_get_role_name($roleid) {
    global $DB;
    // FIX: Using get_record_select with parameter is more secure and reliable.
    $sql = 'id = :roleid';
    // The "unclosed parenthesis" was likely an extra ')' in the previous iteration of this line.
    return $DB->get_record_select('role', $sql, ['roleid' => $roleid]);
}

function quickenrol_get_roles() {
    $roles = array();
    $rolesContext = get_roles_for_contextlevels(CONTEXT_COURSE); 
    foreach ($rolesContext as $roleContext) {
        $role = quickenrol_get_role_name($roleContext);
        $roles[] = $role;
    }
    return $roles;
}

function quickenrol_display_roles() {
    $roles = quickenrol_get_roles();
    $options = '';
    foreach ($roles as $role) {
        $role->name = role_get_name($role);
        $selected = $role->id == 5 ? 'selected' : '';
        $options .= '<option value="' . $role->id . '" ' . $selected . '>' . s($role->name) . '</option>';
    }
    return $options;
}


