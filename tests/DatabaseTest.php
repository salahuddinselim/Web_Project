<?php
require_once __DIR__ . '/../includes/db_functions.php';

class DatabaseTest {
    
    public function testGetMemberProfile() {
        // Based on sample_data.sql, member1 has member_id 1
        $member = getMemberProfile(1);
        assertNotEmpty($member, "Should find member with ID 1");
        assertEqual('member1', $member['username'], "Username should match member1");
    }
    
    public function testGetTrainerMembers() {
        // trainer1 has trainer_id 1
        $members = getTrainerMembers(1);
        assertNotEmpty($members, "Trainer 1 should have assigned members");
    }
    
    public function testGetMemberRoutines() {
        $routines = getMemberRoutines(1);
        assertNotEmpty($routines, "Member 1 should have routines");
    }
}
