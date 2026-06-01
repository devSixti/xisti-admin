<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PageSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $page_settings_record= [
            [
                'id' => 1,
                'name' => 'Contact us',
                'pt_name' => 'Contact us',
                'vi_name' => 'Contact us',
                'he_name' => 'Contact us',
                'de_name' => 'Contact us',
                'es_name' => 'Contact us',
                'fr_name' => 'Contact us',
                'ko_name' => 'Contact us',
                'ja_name' => 'Contact us',
                'zh_name' => 'Contact us',
                'fil_name' => 'Contact us',
                'ar_name' => 'Contact us',
                'lo_name' => 'Contact us',
                'type' => 1,
                'description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">
                                  <span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                  <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">
                                  <span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                  <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Medien- und Geschäftsanfragen</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">
                                    <span style="font-family: Verdana, sans-serif;">Schreiben Sie uns eine E-Mail:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Allgemeine und technische Anfragen</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">
                                    <span style="font-family: Verdana, sans-serif;">Schreiben Sie uns eine E-Mail:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'es_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'fr_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'ko_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'ja_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'zh_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'fil_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                      <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                      <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'ar_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'lo_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',
            ],
            [
                'id' => 2,
                'name' => 'FAQ',
                'pt_name' => 'FAQ',
                'vi_name' => 'FAQ',
                'he_name' => 'FAQ',
                'de_name' => 'FAQ',
                'es_name' => 'FAQ',
                'fr_name' => 'FAQ',
                'ko_name' => 'FAQ',
                'ja_name' => 'FAQ',
                'zh_name' => 'FAQ',
                'fil_name' => 'FAQ',
                'ar_name' => 'FAQ',
                'lo_name' => 'FAQ',
                'type' => 1,
                'description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                     <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>',

                'es_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>',

                'fr_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>',

                'ko_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>',

                'ja_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                     <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                     <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>',

                'zh_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>',

                'fil_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                      <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>',

                'ar_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>',

                'lo_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;">&nbsp;will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>',
            ],
            [
                'id' => 3,
                'name' => 'Disclaimer',
                'pt_name' => 'Disclaimer',
                'vi_name' => 'Disclaimer',
                'he_name' => 'Disclaimer',
                'de_name' => 'Disclaimer',
                'es_name' => 'Disclaimer',
                'fr_name' => 'Disclaimer',
                'ko_name' => 'Disclaimer',
                'ja_name' => 'Disclaimer',
                'zh_name' => 'Disclaimer',
                'fil_name' => 'Disclaimer',
                'ar_name' => 'Disclaimer',
                'lo_name' => 'Disclaimer',
                'type' => 1,
                'description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                  <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                  <p>"service") operated by us.</p>
                                  <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                  <p>reuse, republish, or reprint such content without our written consent.</p>
                                  <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                  <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                  <p>app, you do so at your own risk.</p>
                                  <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                  <p>guarantee that there are no mistakes or errors.</p>
                                  <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                  <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                  <p>you to frequently visit this page.</p>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'es_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                    <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                    <p>"service") operated by us.</p>
                                    <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                    <p>reuse, republish, or reprint such content without our written consent.</p>
                                    <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                    <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                    <p>app, you do so at your own risk.</p>
                                    <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                    <p>guarantee that there are no mistakes or errors.</p>
                                    <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                    <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                    <p>you to frequently visit this page.</p>',

                'fr_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'ko_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'ja_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'zh_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'fil_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'ar_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'lo_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',
            ],
            [
                'id' => 4,
                'name' => 'Privacy Policy',
                'pt_name' => 'Privacy Policy',
                'vi_name' => 'Privacy Policy',
                'he_name' => 'Privacy Policy',
                'de_name' => 'Privacy Policy',
                'es_name' => 'Privacy Policy',
                'fr_name' => 'Privacy Policy',
                'ko_name' => 'Privacy Policy',
                'ja_name' => 'Privacy Policy',
                'zh_name' => 'Privacy Policy',
                'fil_name' => 'Privacy Policy',
                'ar_name' => 'Privacy Policy',
                'lo_name' => 'Privacy Policy',

                'type' => 1,
                'description' => '<div style="margin-left: 20px; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<div style="margin-left: 20px; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'es_description' => '<div style="margin-left: 20px; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'fr_description' => '<div style="margin-left: 20px; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'ko_description' => '<div style="margin-left: 20px; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'ja_description' => '<div style="margin-left: 20px; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'zh_description' => '<div style="margin-left: 20px; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'fil_description' => '<div style="margin-left: 20px; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'ar_description' => '<div style="margin-left: 20px; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'lo_description' => '<div style="margin-left: 20px; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',
            ],
            [
                'id' => 5,
                'name' => 'Terms and Conditions',
                'pt_name' => 'Terms and Conditions',
                'vi_name' => 'Terms and Conditions',
                'he_name' => 'Terms and Conditions',
                'de_name' => 'Terms and Conditions',
                'es_name' => 'Terms and Conditions',
                'fr_name' => 'Terms and Conditions',
                'ko_name' => 'Terms and Conditions',
                'ja_name' => 'Terms and Conditions',
                'zh_name' => 'Terms and Conditions',
                'fil_name' => 'Terms and Conditions',
                'ar_name' => 'Terms and Conditions',
                'lo_name' => 'Terms and Conditions',
                'type' => 1,
                'description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'es_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'fr_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'ko_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'ja_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'zh_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'fil_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'ar_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'lo_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',
            ],
            [
                'id' => 6,
                'name' => 'Contact us',
                'pt_name' => 'Contact us',
                'vi_name' => 'Contact us',
                'he_name' => 'Contact us',
                'de_name' => 'Contact us',
                'es_name' => 'Contact us',
                'fr_name' => 'Contact us',
                'ko_name' => 'Contact us',
                'ja_name' => 'Contact us',
                'zh_name' => 'Contact us',
                'fil_name' => 'Contact us',
                'ar_name' => 'Contact us',
                'lo_name' => 'Contact us',
                'type' => 2,
                'description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'es_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'fr_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'ko_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'ja_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'zh_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'fil_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'ar_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'lo_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',
            ],
            [
                'id' => 7,
                'name' => 'FAQ',
                'pt_name' => 'FAQ',
                'vi_name' => 'FAQ',
                'he_name' => 'FAQ',
                'de_name' => 'FAQ',
                'es_name' => 'FAQ',
                'fr_name' => 'FAQ',
                'ko_name' => 'FAQ',
                'ja_name' => 'FAQ',
                'zh_name' => 'FAQ',
                'fil_name' => 'FAQ',
                'ar_name' => 'FAQ',
                'lo_name' => 'FAQ',
                'type' => 2,
                'description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'es_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'fr_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'ko_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'ja_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'zh_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'fil_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'ar_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'lo_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',
            ],
            [
                'id' => 8,
                'name' => 'Disclaimer',
                'pt_name' => 'Disclaimer',
                'vi_name' => 'Disclaimer',
                'he_name' => 'Disclaimer',
                'de_name' => 'Disclaimer',
                'es_name' => 'Disclaimer',
                'fr_name' => 'Disclaimer',
                'ko_name' => 'Disclaimer',
                'ja_name' => 'Disclaimer',
                'zh_name' => 'Disclaimer',
                'fil_name' => 'Disclaimer',
                'ar_name' => 'Disclaimer',
                'lo_name' => 'Disclaimer',
                'type' => 2,
                'description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                  <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                  <p>"service") operated by us.</p>
                                  <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                  <p>reuse, republish, or reprint such content without our written consent.</p>
                                  <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                  <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                  <p>app, you do so at your own risk.</p>
                                  <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                  <p>guarantee that there are no mistakes or errors.</p>
                                  <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                  <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                  <p>you to frequently visit this page.</p>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'es_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                    <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                    <p>"service") operated by us.</p>
                                    <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                    <p>reuse, republish, or reprint such content without our written consent.</p>
                                    <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                    <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                    <p>app, you do so at your own risk.</p>
                                    <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                    <p>guarantee that there are no mistakes or errors.</p>
                                    <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                    <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                    <p>you to frequently visit this page.</p>',

                'fr_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'ko_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'ja_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'zh_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'fil_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'ar_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'lo_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',
            ],
            [
                'id' => 9,
                'name' => 'Privacy Policy',
                'pt_name' => 'Privacy Policy',
                'vi_name' => 'Privacy Policy',
                'he_name' => 'Privacy Policy',
                'de_name' => 'Privacy Policy',
                'es_name' => 'Privacy Policy',
                'fr_name' => 'Privacy Policy',
                'ko_name' => 'Privacy Policy',
                'ja_name' => 'Privacy Policy',
                'zh_name' => 'Privacy Policy',
                'fil_name' => 'Privacy Policy',
                'ar_name' => 'Privacy Policy',
                'lo_name' => 'Privacy Policy',
                'type' => 2,
                'description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'es_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'fr_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'ko_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'ja_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'zh_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'fil_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'ar_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'lo_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',
            ],
            [
                'id' => 10,
                'name' => 'Terms and Conditions',
                'pt_name' => 'Terms and Conditions',
                'vi_name' => 'Terms and Conditions',
                'he_name' => 'Terms and Conditions',
                'de_name' => 'Terms and Conditions',
                'es_name' => 'Terms and Conditions',
                'fr_name' => 'Terms and Conditions',
                'ko_name' => 'Terms and Conditions',
                'ja_name' => 'Terms and Conditions',
                'zh_name' => 'Terms and Conditions',
                'fil_name' => 'Terms and Conditions',
                'ar_name' => 'Terms and Conditions',
                'lo_name' => 'Terms and Conditions',
                'type' => 2,
                'description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'es_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'fr_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'ko_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'ja_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'zh_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'fil_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'ar_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'lo_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',
            ],
            [
                'id' => 11,
                'name' => 'Contact us',
                'pt_name' => 'Contact us',
                'vi_name' => 'Contact us',
                'he_name' => 'Contact us',
                'de_name' => 'Contact us',
                'es_name' => 'Contact us',
                'fr_name' => 'Contact us',
                'ko_name' => 'Contact us',
                'ja_name' => 'Contact us',
                'zh_name' => 'Contact us',
                'fil_name' => 'Contact us',
                'ar_name' => 'Contact us',
                'lo_name' => 'Contact us',
                'type' => 3,
                'description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">
                                  <span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                  <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">
                                  <span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                  <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Medien- und Geschäftsanfragen</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">
                                    <span style="font-family: Verdana, sans-serif;">Schreiben Sie uns eine E-Mail:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Allgemeine und technische Anfragen</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">
                                    <span style="font-family: Verdana, sans-serif;">Schreiben Sie uns eine E-Mail:&nbsp;</span><span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'es_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'fr_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'ko_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'ja_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'zh_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'fil_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                      <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                      <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                      <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                      <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'ar_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',
                'lo_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                    <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                    <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                    <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',
            ],
            [
                'id' => 12,
                'name' => 'FAQ',
                'pt_name' => 'FAQ',
                'vi_name' => 'FAQ',
                'he_name' => 'FAQ',
                'de_name' => 'FAQ',
                'es_name' => 'FAQ',
                'fr_name' => 'FAQ',
                'ko_name' => 'FAQ',
                'ja_name' => 'FAQ',
                'zh_name' => 'FAQ',
                'fil_name' => 'FAQ',
                'ar_name' => 'FAQ',
                'lo_name' => 'FAQ',
                'type' => 3,
                'description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'es_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'fr_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'ko_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'ja_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'zh_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'fil_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'ar_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',

                'lo_description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Fox-jek</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                   <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                   <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><br /><br /></p>',
            ],
            [
                'id' => 13,
                'name' => 'Disclaimer',
                'pt_name' => 'Disclaimer',
                'vi_name' => 'Disclaimer',
                'he_name' => 'Disclaimer',
                'de_name' => 'Disclaimer',
                'es_name' => 'Disclaimer',
                'fr_name' => 'Disclaimer',
                'ko_name' => 'Disclaimer',
                'ja_name' => 'Disclaimer',
                'zh_name' => 'Disclaimer',
                'fil_name' => 'Disclaimer',
                'ar_name' => 'Disclaimer',
                'lo_name' => 'Disclaimer',
                'type' => 3,
                'description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                  <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                  <p>"service") operated by us.</p>
                                  <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                  <p>reuse, republish, or reprint such content without our written consent.</p>
                                  <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                  <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                  <p>app, you do so at your own risk.</p>
                                  <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                  <p>guarantee that there are no mistakes or errors.</p>
                                  <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                  <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                  <p>you to frequently visit this page.</p>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'es_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                    <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                    <p>"service") operated by us.</p>
                                    <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                    <p>reuse, republish, or reprint such content without our written consent.</p>
                                    <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                    <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                    <p>app, you do so at your own risk.</p>
                                    <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                    <p>guarantee that there are no mistakes or errors.</p>
                                    <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                    <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                    <p>you to frequently visit this page.</p>',

                'fr_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'ko_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'ja_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'zh_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'fil_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'ar_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',

                'lo_description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                     <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                     <p>"service") operated by us.</p>
                                     <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                     <p>reuse, republish, or reprint such content without our written consent.</p>
                                     <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                     <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                     <p>app, you do so at your own risk.</p>
                                     <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                     <p>guarantee that there are no mistakes or errors.</p>
                                     <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                     <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                     <p>you to frequently visit this page.</p>',
            ],
            [
                'id' => 14,
                'name' => 'Privacy Policy',
                'pt_name' => 'Privacy Policy',
                'vi_name' => 'Privacy Policy',
                'he_name' => 'Privacy Policy',
                'de_name' => 'Privacy Policy',
                'es_name' => 'Privacy Policy',
                'fr_name' => 'Privacy Policy',
                'ko_name' => 'Privacy Policy',
                'ja_name' => 'Privacy Policy',
                'zh_name' => 'Privacy Policy',
                'fil_name' => 'Privacy Policy',
                'ar_name' => 'Privacy Policy',
                'lo_name' => 'Privacy Policy',
                'type' => 3,
                'description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'es_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'fr_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'ko_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'ja_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'zh_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'fil_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'ar_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',

                'lo_description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',
            ],
            [
                'id' => 15,
                'name' => 'Terms and Conditions',
                'pt_name' => 'Terms and Conditions',
                'vi_name' => 'Terms and Conditions',
                'he_name' => 'Terms and Conditions',
                'de_name' => 'Terms and Conditions',
                'es_name' => 'Terms and Conditions',
                'fr_name' => 'Terms and Conditions',
                'ko_name' => 'Terms and Conditions',
                'ja_name' => 'Terms and Conditions',
                'zh_name' => 'Terms and Conditions',
                'fil_name' => 'Terms and Conditions',
                'ar_name' => 'Terms and Conditions',
                'lo_name' => 'Terms and Conditions',
                'type' => 3,
                'description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'es_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'fr_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'ko_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'ja_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'zh_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'fil_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'ar_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',

                'lo_description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',
            ],
            [
                'id' => 16,
                'name' => 'Contact us',
                'pt_name' => 'Contact us',
                'vi_name' => 'Contact us',
                'he_name' => 'Contact us',
                'de_name' => 'Contact us',
                'es_name' => 'Contact us',
                'fr_name' => 'Contact us',
                'ko_name' => 'Contact us',
                'ja_name' => 'Contact us',
                'zh_name' => 'Contact us',
                'fil_name' => 'Contact us',
                'ar_name' => 'Contact us',
                'lo_name' => 'Contact us',
                'type' => 4,
                'description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">
                                  <span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                  <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">
                                  <span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                  <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '',
                'es_description' => '',
                'fr_description' => '',
                'ko_description' => '',
                'ja_description' => '',
                'zh_description' => '',
                'fil_description' => '',
                'ar_description' => '',
                'lo_description' => '',
            ],
            [
                'id' => 17,
                'name' => 'FAQ',
                'pt_name' => 'FAQ',
                'vi_name' => 'FAQ',
                'he_name' => 'FAQ',
                'de_name' => 'FAQ',
                'es_name' => 'FAQ',
                'fr_name' => 'FAQ',
                'ko_name' => 'FAQ',
                'ja_name' => 'FAQ',
                'zh_name' => 'FAQ',
                'fil_name' => 'FAQ',
                'ar_name' => 'FAQ',
                'lo_name' => 'FAQ',
                'type' => 4,
                'description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>',
                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '',
                'es_description' => '',
                'fr_description' => '',
                'ko_description' => '',
                'ja_description' => '',
                'zh_description' => '',
                'fil_description' => '',
                'ar_description' => '',
                'lo_description' => '',
            ],
            [
                'id' => 18,
                'name' => 'Disclaimer',
                'pt_name' => 'Disclaimer',
                'vi_name' => 'Disclaimer',
                'he_name' => 'Disclaimer',
                'de_name' => 'Disclaimer',
                'es_name' => 'Disclaimer',
                'fr_name' => 'Disclaimer',
                'ko_name' => 'Disclaimer',
                'ja_name' => 'Disclaimer',
                'zh_name' => 'Disclaimer',
                'fil_name' => 'Disclaimer',
                'ar_name' => 'Disclaimer',
                'lo_name' => 'Disclaimer',
                'type' => 4,
                'description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                  <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                  <p>"service") operated by us.</p>
                                  <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                  <p>reuse, republish, or reprint such content without our written consent.</p>
                                  <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                  <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                  <p>app, you do so at your own risk.</p>
                                  <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                  <p>guarantee that there are no mistakes or errors.</p>
                                  <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                  <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                  <p>you to frequently visit this page.</p>',
                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '',
                'es_description' => '',
                'fr_description' => '',
                'ko_description' => '',
                'ja_description' => '',
                'zh_description' => '',
                'fil_description' => '',
                'ar_description' => '',
                'lo_description' => '',

            ],
            [
                'id' => 19,
                'name' => 'Privacy Policy',
                'pt_name' => 'Privacy Policy',
                'vi_name' => 'Privacy Policy',
                'he_name' => 'Privacy Policy',
                'de_name' => 'Privacy Policy',
                'es_name' => 'Privacy Policy',
                'fr_name' => 'Privacy Policy',
                'ko_name' => 'Privacy Policy',
                'ja_name' => 'Privacy Policy',
                'zh_name' => 'Privacy Policy',
                'fil_name' => 'Privacy Policy',
                'ar_name' => 'Privacy Policy',
                'lo_name' => 'Privacy Policy',
                'type' => 4,
                'description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',
                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '',
                'es_description' => '',
                'fr_description' => '',
                'ko_description' => '',
                'ja_description' => '',
                'zh_description' => '',
                'fil_description' => '',
                'ar_description' => '',
                'lo_description' => '',
            ],
            [
                'id' => 20,
                'name' => 'Terms and Conditions',
                'pt_name' => 'Terms and Conditions',
                'vi_name' => 'Terms and Conditions',
                'he_name' => 'Terms and Conditions',
                'de_name' => 'Terms and Conditions',
                'es_name' => 'Terms and Conditions',
                'fr_name' => 'Terms and Conditions',
                'ko_name' => 'Terms and Conditions',
                'ja_name' => 'Terms and Conditions',
                'zh_name' => 'Terms and Conditions',
                'fil_name' => 'Terms and Conditions',
                'ar_name' => 'Terms and Conditions',
                'lo_name' => 'Terms and Conditions',
                'type' => 4,
                'description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',
                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '',
                'es_description' => '',
                'fr_description' => '',
                'ko_description' => '',
                'ja_description' => '',
                'zh_description' => '',
                'fil_description' => '',
                'ar_description' => '',
                'lo_description' => '',

            ],
            [
                'id' => 21,
                'name' => 'Contact us',
                'pt_name' => 'Contact us',
                'vi_name' => 'Contact us',
                'he_name' => 'Contact us',
                'de_name' => 'Contact us',
                'es_name' => 'Contact us',
                'fr_name' => 'Contact us',
                'ko_name' => 'Contact us',
                'ja_name' => 'Contact us',
                'zh_name' => 'Contact us',
                'fil_name' => 'Contact us',
                'ar_name' => 'Contact us',
                'lo_name' => 'Contact us',
                'type' => 5,
                'description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Media and Business Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">
                                  <span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                  <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">General and Technical Inquiries</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">
                                  <span style="font-family: Verdana, sans-serif;">Email us:&nbsp;</span>
                                  <span style="background-color: #ffffff; color: #333333; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px;">zimoapp@gmail.com.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">&nbsp;&nbsp;</span></p>',

                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '',
                'es_description' => '',
                'fr_description' => '',
                'ko_description' => '',
                'ja_description' => '',
                'zh_description' => '',
                'fil_description' => '',
                'ar_description' => '',
                'lo_description' => '',
            ],
            [
                'id' => 22,
                'name' => 'FAQ',
                'pt_name' => 'FAQ',
                'vi_name' => 'FAQ',
                'he_name' => 'FAQ',
                'de_name' => 'FAQ',
                'es_name' => 'FAQ',
                'fr_name' => 'FAQ',
                'ko_name' => 'FAQ',
                'ja_name' => 'FAQ',
                'zh_name' => 'FAQ',
                'fil_name' => 'FAQ',
                'ar_name' => 'FAQ',
                'lo_name' => 'FAQ',
                'type' => 5,
                'description' => '<p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Why us?</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;"> is a multi-service platform that provides an all-in-one solution to consumers and businesses.&nbsp;</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">XISTI</span></strong><span style="font-family: Verdana, sans-serif;"> will be a valued partner to our potential clients delivering turnkey solutions and measurable results.&nbsp;</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We&nbsp;<strong>CONNECT BUSINESSES</strong>&nbsp;with their customer base and help acquire new ones.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We build&nbsp;<strong>INNOVATIVE AND FULLY INTEGRATED SOLUTIONS</strong>&nbsp;to help increase our clients&rsquo; brand and enhance user experience.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are&nbsp;<strong>RESULT ORIENTED</strong>&nbsp;and will provide a framework to attain digital transformation on your business methodologies.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;"><span style="font-family: Wingdings;">+</span><span style="font-family: Times New Roman, serif;">&nbsp;</span><span style="font-family: Verdana, sans-serif;">We are your&nbsp;<strong>TECHNOLOGY APP PLATFORM PARTNER</strong>. Your way, anytime, anywhere.</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt 0.5in; font-size: medium; font-family: Calibri, sans-serif; text-indent: -0.25in;">&nbsp;</p>
                                  <div class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif; text-align: center;" align="center"><hr style="color: #a0a0a0;" align="center" noshade="noshade" size="1" width="100%" data-darkreader-inline-color="" /></div>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><strong><span style="font-family: Verdana, sans-serif;">Is it available now?</span></strong></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Yes, join us and download apps! Sign up and share us with your friends and family.&nbsp;</span></p>
                                  <p class="MsoNormal" style="margin: 0in 0in 0.0001pt; font-size: medium; font-family: Calibri, sans-serif;">&nbsp;</p>',
                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '',
                'es_description' => '',
                'fr_description' => '',
                'ko_description' => '',
                'ja_description' => '',
                'zh_description' => '',
                'fil_description' => '',
                'ar_description' => '',
                'lo_description' => '',
            ],
            [
                'id' => 23,
                'name' => 'Disclaimer',
                'pt_name' => 'Disclaimer',
                'vi_name' => 'Disclaimer',
                'he_name' => 'Disclaimer',
                'de_name' => 'Disclaimer',
                'es_name' => 'Disclaimer',
                'fr_name' => 'Disclaimer',
                'ko_name' => 'Disclaimer',
                'ja_name' => 'Disclaimer',
                'zh_name' => 'Disclaimer',
                'fil_name' => 'Disclaimer',
                'ar_name' => 'Disclaimer',
                'lo_name' => 'Disclaimer',
                'type' => 5,
                'description' => '<p>&nbsp;Disclaimer&nbsp;</p>
                                  <p>Please read this disclaimer ("disclaimer") carefully before using app (&ldquo;app&rdquo;,</p>
                                  <p>"service") operated by us.</p>
                                  <p>The content displayed on the app is the intellectual property of the app. You may not</p>
                                  <p>reuse, republish, or reprint such content without our written consent.</p>
                                  <p>All information posted is merely for educational and informational purposes. It is not intended</p>
                                  <p>as a substitute for professional advice. Should you decide to act upon any information on this</p>
                                  <p>app, you do so at your own risk.</p>
                                  <p>While the information on this app has been verified to the best of our abilities, we cannot</p>
                                  <p>guarantee that there are no mistakes or errors.</p>
                                  <p>We reserve the right to change this policy at any given time, of which you will be promptly</p>
                                  <p>updated. If you want to make sure that you are up to date with the latest changes, we advise</p>
                                  <p>you to frequently visit this page.</p>',
                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '',
                'es_description' => '',
                'fr_description' => '',
                'ko_description' => '',
                'ja_description' => '',
                'zh_description' => '',
                'fil_description' => '',
                'ar_description' => '',
                'lo_description' => '',

            ],
            [
                'id' => 24,
                'name' => 'Privacy Policy',
                'pt_name' => 'Privacy Policy',
                'vi_name' => 'Privacy Policy',
                'he_name' => 'Privacy Policy',
                'de_name' => 'Privacy Policy',
                'es_name' => 'Privacy Policy',
                'fr_name' => 'Privacy Policy',
                'ko_name' => 'Privacy Policy',
                'ja_name' => 'Privacy Policy',
                'zh_name' => 'Privacy Policy',
                'fil_name' => 'Privacy Policy',
                'ar_name' => 'Privacy Policy',
                'lo_name' => 'Privacy Policy',
                'type' => 5,
                'description' => '<div style="margin-left: 20px; color: black; background-color: ghostwhite; padding-top: 20px;">
                                <p><strong style="font-family: Calibri, sans-serif;"><span style="font-family: Verdana, sans-serif;">Privacy Policy</span></strong></p>
                                <p>XISTI built the XISTI app as open-source/free app. This Privacy Policy for that XISTI (https://admin.xistiapp.com/) will collect the personal information like name, email, contacy number, etc when you use our mobile application.</p>
                                <p><strong>Information Collection and Use</strong></p>
                                <p>For a better experience, while using our Service, We may require you to provide us with certain personally identifiable information add whatever else you collect here(in https://admin.xistiapp.com/), e.g. users name, address, location, pictures The information that We request will be retained on your device and is not collected by us in any way/[retained by us and used as described in this privacy policy.</p>
                                <p>The collected information is shared with third-party services because using the customer data we can provide personalize app behavour, our service &amp; product improvement.</p>
                                <p><strong>Log Data</strong></p>
                                <p>We want to inform you that whenever you use our Service, in a case of an error in the app We collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing [my/our] Service, the time and date of your use of the Service, and other statistics.</p>
                                <p><strong>Cookies</strong></p>
                                <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your devices internal memory.</p>
                                <p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>
                                <p><strong>Service Providers</strong></p>
                                <p>We may employ third-party companies and individuals due to the following reasons:</p>
                                <ul>
                                <li>To facilitate our Service;</li>
                                <li>To provide the Service on our behalf;</li>
                                <li>To perform Service-related services; or</li>
                                <li>To assist us in analyzing how our Service is used.</li>
                                </ul>
                                <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>
                                <p><strong>Security</strong></p>
                                <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and We cannot guarantee its absolute security.</p>
                                <p><strong>Changes to This Privacy Policy</strong></p>
                                <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                                <p><strong>Contact Us</strong></p>
                                <p>If you have any questions or suggestions about our] Privacy Policy, do not hesitate to contact us at zimoapp@gmail.com.</p>
                                </div>',
                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '',
                'es_description' => '',
                'fr_description' => '',
                'ko_description' => '',
                'ja_description' => '',
                'zh_description' => '',
                'fil_description' => '',
                'ar_description' => '',
                'lo_description' => '',
            ],
            [
                'id' => 25,
                'name' => 'Terms and Conditions',
                'pt_name' => 'Terms and Conditions',
                'vi_name' => 'Terms and Conditions',
                'he_name' => 'Terms and Conditions',
                'de_name' => 'Terms and Conditions',
                'es_name' => 'Terms and Conditions',
                'fr_name' => 'Terms and Conditions',
                'ko_name' => 'Terms and Conditions',
                'ja_name' => 'Terms and Conditions',
                'zh_name' => 'Terms and Conditions',
                'fil_name' => 'Terms and Conditions',
                'ar_name' => 'Terms and Conditions',
                'lo_name' => 'Terms and Conditions',
                'type' => 5,
                'description' => '<div style="padding: 0px 50px;">
                                  <p><strong>What personal information do we collect?</strong></p>
                                  <p>When you place an order or complete a customer survey, we may collect personal information about you which may include name, email address, telephone number, location etc when voluntarily given by you. We collect this information to carry out the services offered by our app and to provide you offers and information about other services you may be interested in.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Who will see my personal information?</strong></p>
                                  <p>Your privacy is of the utmost importance to us and no sensitive data will be shared without your consent.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Is my personal information secure with XISTI?</strong></p>
                                  <p>XISTI will endeavor to protect your personal information from interference, modification, disclosure, misuse, loss, and unauthorized access. You are responsible for the confidentiality of your password and we strongly recommend against sharin</p>
                                  </div>',
                'pt_description' => null,
                'vi_description' => null,
                'he_description' => null,
                'de_description' => '',
                'es_description' => '',
                'fr_description' => '',
                'ko_description' => '',
                'ja_description' => '',
                'zh_description' => '',
                'fil_description' => '',
                'ar_description' => '',
                'lo_description' => '',
            ],

        ];

        $removedLanguages = ['vi', 'he', 'de', 'ko', 'ja', 'zh', 'fil', 'ar', 'lo'];
        $supportedLanguages = ['pt', 'es', 'fr'];
        $hasItalian = Schema::hasColumn('page_settings', 'it_name') && Schema::hasColumn('page_settings', 'it_description');

        foreach ($page_settings_record as &$record) {
            foreach ($removedLanguages as $language) {
                unset($record["{$language}_name"], $record["{$language}_description"]);
            }

            foreach ($supportedLanguages as $language) {
                if (!isset($record["{$language}_name"]) || $record["{$language}_name"] === '') {
                    $record["{$language}_name"] = $record['name'];
                }

                if (!isset($record["{$language}_description"]) || $record["{$language}_description"] === '') {
                    $record["{$language}_description"] = $record['description'];
                }
            }

            if ($hasItalian) {
                $record['it_name'] = $record['it_name'] ?? $record['name'];
                $record['it_description'] = $record['it_description'] ?? $record['description'];
            }
        }
        unset($record);

        $updateColumns = ['name', 'pt_name', 'es_name', 'fr_name', 'type', 'description', 'pt_description', 'es_description', 'fr_description'];

        if ($hasItalian) {
            $updateColumns[] = 'it_name';
            $updateColumns[] = 'it_description';
        }

        /*
       | upsert
       |--------------------------------------------------------------------------
       | We are using upsert here as it functions to either insert or update records efficiently.
       | If a record already exists, it updates it; if not, it inserts a new record.
       | This operation compares records using a unique key and supports handling multiple records in a single operation.
       */
        DB::table('page_settings')->upsert(
            $page_settings_record,
            ['id'], // Unique column to determine if a row exists
            $updateColumns
        );

    }
}
