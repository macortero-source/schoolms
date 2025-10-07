<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = User::where('role', 'admin')->pluck('id');

        if ($admins->isEmpty()) {
            $this->command->error('No admin users found!');
            return;
        }

        $announcements = [
            // Recent Announcements
            [
                'title' => 'School Resumption Notice',
                'content' => 'All students are hereby informed that the school will resume for the new academic session on Monday, January 15th, 2025. Students are expected to report with their complete uniforms and required materials.',
                'target_audience' => 'students',
                'priority' => 'high',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(5),
                'expiry_date' => Carbon::now()->addDays(10),
                'send_email' => true,
                'email_sent' => true,
            ],
            [
                'title' => 'Parent-Teacher Meeting',
                'content' => 'The school management invites all parents to a general meeting on Saturday, October 7th, 2025 at 10:00 AM in the school hall. Attendance is mandatory. Topics include academic performance review and upcoming events.',
                'target_audience' => 'parents',
                'priority' => 'urgent',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(3),
                'expiry_date' => Carbon::now()->addDays(4),
                'send_email' => true,
                'email_sent' => true,
            ],
            [
                'title' => 'Staff Development Workshop',
                'content' => 'All teaching staff are required to attend the professional development workshop on "Modern Teaching Methodologies" scheduled for Friday, October 6th, 2025. The workshop will run from 2:00 PM to 5:00 PM.',
                'target_audience' => 'teachers',
                'priority' => 'high',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(7),
                'expiry_date' => Carbon::now()->addDays(3),
                'send_email' => true,
                'email_sent' => true,
            ],
            [
                'title' => 'Mid-Term Examination Schedule',
                'content' => 'The mid-term examinations will commence on October 16th, 2025 and conclude on October 20th, 2025. Students are advised to prepare adequately. The detailed timetable will be posted on notice boards and sent to your emails.',
                'target_audience' => 'all',
                'priority' => 'high',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(10),
                'expiry_date' => Carbon::now()->addDays(13),
                'send_email' => true,
                'email_sent' => true,
            ],
            [
                'title' => 'Sports Day Event',
                'content' => 'The annual inter-house sports competition will be held on October 21st, 2025. All students are expected to participate. Parents are invited to attend and cheer their children. Events include athletics, football, volleyball, and more.',
                'target_audience' => 'all',
                'priority' => 'medium',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(12),
                'expiry_date' => Carbon::now()->addDays(18),
                'send_email' => false,
                'email_sent' => false,
            ],
            [
                'title' => 'Library Opening Hours Extended',
                'content' => 'The school library will now be open from 7:00 AM to 7:00 PM on weekdays to accommodate students who wish to study. The library remains closed on weekends and public holidays.',
                'target_audience' => 'students',
                'priority' => 'low',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(15),
                'expiry_date' => null,
                'send_email' => false,
                'email_sent' => false,
            ],
            [
                'title' => 'COVID-19 Safety Protocols',
                'content' => 'All students, staff, and visitors must adhere to COVID-19 safety protocols: wear face masks, maintain social distancing, and use hand sanitizers available at various points in the school.',
                'target_audience' => 'all',
                'priority' => 'urgent',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(30),
                'expiry_date' => null,
                'send_email' => true,
                'email_sent' => true,
            ],
            [
                'title' => 'School Fee Payment Deadline',
                'content' => 'Parents are reminded that the deadline for school fee payment for the current term is October 10th, 2025. Late payment will attract a 10% penalty. Payment can be made at the school bursar office or via bank transfer.',
                'target_audience' => 'parents',
                'priority' => 'high',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(8),
                'expiry_date' => Carbon::now()->addDays(7),
                'send_email' => true,
                'email_sent' => true,
            ],
            [
                'title' => 'Computer Lab Maintenance',
                'content' => 'The computer laboratory will be closed for maintenance and upgrades from October 5th to October 8th, 2025. Computer Science classes will be rescheduled. We apologize for any inconvenience.',
                'target_audience' => 'students',
                'priority' => 'medium',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(2),
                'expiry_date' => Carbon::now()->addDays(5),
                'send_email' => false,
                'email_sent' => false,
            ],
            [
                'title' => 'Excursion to National Museum',
                'content' => 'SSS 2 students will be going on an educational excursion to the National Museum on October 14th, 2025. Permission slips must be signed by parents and returned by October 9th. Cost: ₦5,000 per student.',
                'target_audience' => 'all',
                'priority' => 'medium',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(6),
                'expiry_date' => Carbon::now()->addDays(11),
                'send_email' => true,
                'email_sent' => true,
            ],
            
            // Expired Announcement
            [
                'title' => 'First Term Graduation Ceremony',
                'content' => 'Congratulations to all SSS 3 students on completing their final examinations. The graduation ceremony was held successfully on September 20th, 2025. We wish all graduates the best in their future endeavors.',
                'target_audience' => 'all',
                'priority' => 'high',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(40),
                'expiry_date' => Carbon::now()->subDays(10),
                'send_email' => true,
                'email_sent' => true,
            ],
            
            // Scheduled Future Announcement
            [
                'title' => 'End of Term Examinations',
                'content' => 'End of term examinations will commence on November 20th, 2025. Students should begin their preparations early. Study materials and past questions are available at the school bookshop.',
                'target_audience' => 'students',
                'priority' => 'high',
                'is_active' => true,
                'publish_date' => Carbon::now()->addDays(20),
                'expiry_date' => Carbon::now()->addDays(60),
                'send_email' => true,
                'email_sent' => false,
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create(array_merge($announcement, [
                'posted_by' => $admins->random(),
            ]));
        }

        $this->command->info('Announcements seeded successfully!');
        $this->command->info('Total announcements: ' . count($announcements));
    }
}