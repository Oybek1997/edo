<template>
  <div class="fullHeight" style="background: #f1f5f8;">
    <div style="position: relative;">
      <v-row class="mx-0" style="position: fixed; width: 79%; z-index: 999;">
        <v-col cols="12" class="ma-0 pa-0 profile_header">
          <v-row class="mx-0 pa-1 background-overlay" style="min-height: 90px">
            <v-col cols="9" class="pa-0 relative">
            </v-col>
            <v-col cols="3" class="pa-0 text-center">
              <v-btn style="background: #fff; height: 20px; padding: 0px 8px; letter-spacing: 0;" small class="btnEditphoto">{{ $t("new_profile.edit_profilephoto") }}</v-btn>             
                <!-- <v-icon style="font-size: 24px; color: #000;" > mdi-settings-helper</v-icon> -->
                <span class="ml-5" style="color: #000; font-size: 40px; line-height: 0; cursor: pointer; display: inline-block;">...</span>
            </v-col>
          </v-row>
        </v-col>
        <v-col cols="12" class="ma-0 pa-0 profile_header_bottom">
          <v-row class="mx-0 px-3 pt-0 pb-0">
            <v-col sm="12" md="1" class="pa-0 mx-0"></v-col>
            <v-col sm="12" md="4" class="pa-0">
              <v-list-item two-line class="px-0 ml-4">
                <v-list-item-avatar
                  width="120"
                  height="120"
                  style="top: -80px; left: -130px; position: absolute;"
                  class="ma-0"
                >
                  <img
                    style="border: 7px solid #B5C3CF;"
                    v-if="base64"
                    :src="'data:application/jpg;base64,' + base64"
                    contain
                  />
                  <img style="border: 5px solid #B5C3CF;" v-else src="../../assets/User-Default.jpg" />
                </v-list-item-avatar>
                <v-list-item-content class="pl-0 pt-0 pb-0" style="position: absolute; top: -20px;">
                  <v-list-item-title class="profile_name mb-2">
                    {{ employee["lastname_" + $i18n.locale] ? employee["lastname_" + $i18n.locale] : '' }}
                    {{ employee["firstname_" + $i18n.locale] ? employee["firstname_" + $i18n.locale] : '' }}
                    {{ employee["middlename_" + $i18n.locale] ? employee["middlename_" + $i18n.locale] : '' }}
                    <router-link
                      :to="'/users/all-users/' + employee_tabel"
                      style="text-decoration: none"
                    >
                      <v-icon color="#00b950" small class="mx-5">mdi-pencil</v-icon>
                    </router-link>
                  </v-list-item-title>
                  <v-list-item-subtitle
                    v-if="roles.length >= 0"
                    class="profile_role"
                    style="max-width: 170px; cursor: pointer; border-radius: 10px; background: #CCE6FF; padding: 2px 10px;"
                    @click="roleDialog = true"
                  >
                    
                    <span v-if="roles[0]"> {{ $t("profile.role") }} {{ roles[0].display_name ? roles[0].display_name : ''}}</span>
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col sm="12" md="3" class="pa-0 mx-0">
              <!-- <span class="ma-0 pa-0" fab dark x-small>
                <img
                  v-if="employee && user && employee.id == user.employee_id"
                  src="img/imzo.png"
                  height="25"
                  style="cursor: pointer; border: 2px solid #faa"
                  @click="eImzoDialog = true"
                />
              </span> -->
            </v-col>
            <v-col sm="12" md="4" class="pa-0 mx-0" style="position: relative; top: -12px;">
              <v-btn elevation="0" style="background: #fff; height: 25px; padding: 0px 8px; letter-spacing: 0;" small class="btnNext mr-2">
                <v-icon style="font-weight: 600; margin: 0;" left > 
                  mdi-chevron-left 
                </v-icon>
                {{ $t("new_profile.previous") }}
              </v-btn>
              <v-btn elevation="0" style="background: #fff; height: 25px; padding: 0px 8px; letter-spacing: 0;" small class="btnNext">
                {{ $t("new_profile.next") }} 
                <v-icon style="font-weight: 600; margin: 0;" right >
                   mdi-chevron-right 
                </v-icon>
              </v-btn>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </div>
    <div class="mx-0" style="position: relative; top: 140px;">
      <v-row class="mx-0 px-0">
        <v-col sm="12" md="12" class="px-2 py-1 d-flex align-center" style="box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2); position: sticky; top: 140px; z-index: 999; height: 35px; background: #fff;">
          <v-tabs
            v-model="tab"
            background-color="transparent"
            color="basil"
            class="profileTab"
            style="height: 35px;"
          >
            <v-tabs-slider class="underLineNone" style="height: 35px;"></v-tabs-slider>
            <v-tab href="#tab-0" style="height: 35px;">{{ $t("profile.personal") }}</v-tab>
            <v-tab href="#tab-1" style="height: 35px;">{{ $t("profile.work") }}</v-tab>
            <v-tab href="#tab-2" style="height: 35px;">{{ $t("profile.absence") }}</v-tab>
            <v-tab href="#tab-3" style="height: 35px;">{{ $t("profile.effectiveness") }}</v-tab>
            <v-tab href="#tab-4" style="height: 35px;">{{ $t("profile.documents") }}</v-tab>
            <v-tab href="#tab-5" style="height: 35px;">{{ $t("profile.resume") }}</v-tab>
            <v-tab href="#tab-6" style="height: 35px;">{{ $t("profile.holidays") }}</v-tab>
            <v-tab href="#tab-7" style="height: 35px;">{{ $t("Material javobgarligi") }}</v-tab>
            <v-tab href="#tab-8" style="height: 35px;">{{ $t("Org texnika") }}</v-tab>
          </v-tabs>
        </v-col>        
        <v-col sm="12" md="9" class="px-5 py-3" style="background: #fff;">
          <v-row class="mx-0" v-if="employee || (employee.user && !employee.user.type)">
            <v-col md="12" class="pa-0">
              <v-tabs-items v-model="tab" style="background: #fff">
                <v-tab-item value="tab-0" class="mx-0 pa-0">
                  <v-card
                    class
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px;">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            <span>{{ $t("profile.personal") }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            {{ $t("profile.employee_id") }}
                            <span>{{ employee.id ? employee.id : '' }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            {{ $t("profile.last_name") }}
                            <span>{{ employee["lastname_" + $i18n.locale] ? employee["lastname_" + $i18n.locale] : '' }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            {{ $t("profile.fist_name") }}
                            <span>{{ employee["firstname_" + $i18n.locale] ? employee["firstname_" + $i18n.locale] : '' }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            {{ $t("profile.email") }}
                            <span>{{ employee.user && employee.user.email ? employee.user.email : "" }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            {{ $t("profile.born_date") }}
                            <span>{{ employee.born_date ? employee.born_date : '' }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            {{ $t("profile.sex") }}
                            <span v-if="employee.gender == 1 || employee.gender == 'M'">{{ $t("profile.male") }}</span>
                            <span v-else-if="employee.gender == 2">{{ $t("profile.fmale") }}</span>
                            <span v-else>{{ $t("profile.noDataText") }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                  <v-card class="mt-4" elevation="0" style="border-radius:10px; border: 1px solid #dce5ef;">
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            <span>{{ $t("profile.social_media") }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.facebook") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.linkedIn") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.instagram") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.twitter") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                  <v-card
                    class="mt-4"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            <span>{{ $t("profile.skills") }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.noDataText") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                  <v-card
                    class="mt-4"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item-group class="list-group">
                        <v-list-item style="min-height: 38px">
                          <v-list-item-content class="py-0 list-text">
                            <v-list-item-title>
                              <span>{{ $t("profile.education") }}</span>
                            </v-list-item-title>
                          </v-list-item-content>
                        </v-list-item>
                      </v-list-item-group>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.noDataText") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                  <v-card
                    class="mt-4"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            <span>{{ $t("profile.licenses") }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.noDataText") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                  <v-card
                    class="my-4"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            <span>{{ $t("profile.other") }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            {{ $t("profile.INN") }}
                            <span>{{ employee.INN ? employee.INN : "" }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            {{ $t("profile.INPS") }}
                            <span>{{ employee.INPS ? employee.INPS : "" }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.passport_data") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>....</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                </v-tab-item>
                <v-tab-item value="tab-1" class="mx-0 pa-0">
                  <v-card
                    class
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px;">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title style="display: flex;">
                            <span style="display: flex; align-items: center;">{{ $t("profile.work") }}</span>
                            <v-spacer></v-spacer>
                            <v-btn elevation="0" style="background: #fff; height: 25px; padding: 0px 8px; letter-spacing: 0;" small class="btnCreate">
                              <v-icon size="15" style="font-weight: 900; margin: 0;" left > 
                                mdi-plus
                              </v-icon>
                              {{ $t("Добавить") }}
                            </v-btn>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.first_work_date") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-btn small color="success" dark class="btnSave mb-3" style>{{ $t("profile.save") }}</v-btn>
                    </v-list>
                  </v-card>
                  <v-card
                    class="mt-4"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title style="display: flex;">
                            <span style="display: flex; align-items: center;">{{ $t("profile.employment") }}</span>
                            <v-spacer></v-spacer>
                            <v-btn elevation="0" style="background: #fff; height: 25px; padding: 0px 8px; letter-spacing: 0;" small class="btnCreate">
                              <v-icon size="15" style="font-weight: 900; margin: 0;" left > 
                                mdi-plus
                              </v-icon>
                              {{ $t("Добавить") }}
                            </v-btn>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.noDataText") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                  <v-card
                    class="mt-4"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title style="display: flex;">
                            <span style="display: flex; align-items: center;">{{ $t("profile.position") }}</span>
                            <v-spacer></v-spacer>
                            <v-btn elevation="0" style="background: #fff; height: 25px; padding: 0px 8px; letter-spacing: 0;" small class="btnCreate">
                              <v-icon size="15" style="font-weight: 900; margin: 0;" left > 
                                mdi-plus
                              </v-icon>
                              {{ $t("Добавить") }}
                            </v-btn>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.noDataText") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                  <v-card
                    class="mt-4"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title style="display: flex;">
                            <span style="display: flex; align-items: center;">{{ $t("profile.Compensation") }}</span>
                            <v-spacer></v-spacer>
                            <v-btn elevation="0" style="background: #fff; height: 25px; padding: 0px 8px; letter-spacing: 0;" small class="btnCreate">
                              <v-icon size="15" style="font-weight: 900; margin: 0;" left > 
                                mdi-plus
                              </v-icon>
                              {{ $t("Добавить") }}
                            </v-btn>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.wage") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.noDataText") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                  <v-card 
                      class="mt-4" 
                      elevation="0"
                      style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                      >                      
                    <v-simple-table v-if="employee_work_histories" fixed-header class="mt-1 pt-1">
                      <template v-slot:default>
                        <thead>
                          <tr>
                            <th class="text-left">#</th>
                            <th class="text-left">{{ $t("Boshlash sanasi") }}</th>
                            <th class="text-left">{{ $t("Tugash sanasi") }}</th>
                            <th class="text-left">{{ $t("employee.work_place") }}</th>
                            <th class="text-left">{{ $t("employee.position") }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(itmw, eWork) in employee_work_histories" :key="eWork">
                            <td class>{{ eWork + 1 }}</td>
                            <td class>{{ itmw.begin_date ? itmw.begin_date : "" }}</td>
                            <td class>{{ itmw.end_date ? itmw.end_date : "" }}</td>
                            <td class>{{ itmw.work_place ? itmw.work_place : "" }}</td>
                            <td class>{{ itmw.position ? itmw.position : "" }}</td>
                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </v-card>
                  <v-card
                    class="mt-4 mb-4"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                    v-if="historys.length"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            <span>{{ $t("profile.position") }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list
                      v-for="(history, index) in historys"
                      :key="index"
                      class="py-0 px-3 list-group"
                    >
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <span>
                            {{ history.enter_order_date }}
                            {{ history.leave_order_date }}
                          </span>
                          <v-list-item-title>
                            {{
                            history.staff.department.department_code
                            ? history.staff.department.department_code
                            : ""
                            }}
                          </v-list-item-title>
                          {{
                          history.staff.position["name_" + $i18n.locale]
                          }}
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                </v-tab-item>
                <v-tab-item value="tab-2" class="mx-0 pa-0" style="min-height: 700px;">
                  <v-card class="pa-10 text-center" elevation="0" style="border-radius:10px; border: 1px solid #dce5ef;">
                    <v-list class="pa-0 mb-6 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            <span
                              style="color: black; font-size: 24px; font-weight: 500; word-wrap: break-word;"
                            >{{ $t("profile.absence_policies") }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-btn
                      style="color: #476887; font-size: 16px; font-weight: 600; word-wrap: break-word; text-transform: none; font-family: Helvetica, Arial, sans-serif;"
                    >{{ $t("profile.assign_policy") }}</v-btn>
                  </v-card>
                </v-tab-item>
                <v-tab-item value="tab-3" class="mx-0 pa-0" style="min-height: 700px;">
                  <v-card
                    class=""
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            <span>{{ $t("profile.kpi") }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.noDataText") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                  <v-card
                    class="mt-4"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            <span>{{ $t("profile.goals") }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.noDataText") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                  <v-card
                    class="mt-4"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            <span>1-on-1</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.noDataText") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                  <v-card
                    class="mt-4"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>
                            <span>{{ $t("profile.competencies") }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <v-divider color="#DCE5EF" class></v-divider>
                    <v-list class="py-0 px-3 list-group">
                      <v-list-item style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title>{{ $t("profile.noDataText") }}</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                </v-tab-item>
                <v-tab-item value="tab-4" class="mx-0 pa-0" style="min-height: 700px;">
                  <v-card
                    v-if="allDocuments"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-simple-table dense fixed-header class="pa-3">
                      <template v-slot:default>
                        <thead>
                          <tr>
                            <th class="text-left">№</th>
                            <th class="text-left">{{ $t("document.document_number") }}</th>
                            <th class="text-left">{{ $t("document.document_date") }}</th>
                            <th class="text-left">{{ $t("document.document_template") }}</th>
                            <th class="text-left">{{ $t("document.document_type_id") }}</th>
                            <th class="text-left">{{ $t("document.status") }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(item, indAllDoc) in allDocuments" :key="indAllDoc">
                            <td v-if="item.status !== 0">{{ indAllDoc + 1 }}</td>
                            <td v-if="item.status !== 0">
                              <router-link
                                :to="
                                  '/documents/show-only-pdf/' +
                                  item.pdf_file_name
                                "
                                style="text-decoration: none"
                              >{{ item.document_number }}</router-link>
                            </td>
                            <td v-if="item.status !== 0">{{ item.document_date }}</td>
                            <td v-if="item.status !== 0">
                              {{
                              item.document_template["name_" + $i18n.locale]
                              }}
                            </td>
                            <td
                              v-if="item.status !== 0"
                            >{{ item.document_type["name_" + $i18n.locale] }}</td>
                            <td v-if="item.status !== 0">
                              <span v-if="item.status == 0">
                                {{
                                document_status[item.status]
                                ? document_status[item.status][
                                "name_" + $i18n.locale
                                ]
                                : ""
                                }}
                              </span>
                              <span v-if="item.status == 1">
                                {{
                                document_status[item.status]
                                ? document_status[item.status][
                                "name_" + $i18n.locale
                                ]
                                : ""
                                }}
                              </span>
                              <span v-if="item.status == 2">
                                {{
                                document_status[item.status]
                                ? document_status[item.status][
                                "name_" + $i18n.locale
                                ]
                                : ""
                                }}
                              </span>
                              <span v-if="item.status == 3">
                                {{
                                document_status[item.status]
                                ? document_status[item.status][
                                "name_" + $i18n.locale
                                ]
                                : ""
                                }}
                              </span>
                              <span v-if="item.status == 4">
                                {{
                                document_status[item.status]
                                ? document_status[item.status][
                                "name_" + $i18n.locale
                                ]
                                : ""
                                }}
                              </span>
                              <span v-if="item.status == 5">
                                {{
                                document_status[item.status]
                                ? document_status[item.status][
                                "name_" + $i18n.locale
                                ]
                                : ""
                                }}
                              </span>
                              <span v-if="item.status == 6">
                                {{
                                document_status[item.status]
                                ? document_status[item.status][
                                "name_" + $i18n.locale
                                ]
                                : ""
                                }}
                              </span>
                            </td>
                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </v-card>
                  <v-card v-else class="py-8" color="basil" flat>
                    <v-alert
                      class="d-flex justify-center rounded-0 py-5"
                      text
                      type="error"
                      icon="mdi-alert-outline"
                    >{{ $t("noDataText") }}</v-alert>
                  </v-card>
                </v-tab-item>
                <v-tab-item value="tab-5" class="mx-0 pa-0" v-if="pdfBase64">
                  <iframe
                    :src="'data:application/pdf;base64,' + pdfBase64"
                    height="1000"
                    width="100%"
                  ></iframe>
                </v-tab-item>
                <v-tab-item value="tab-6" class="mx-0 pa-0" style="min-height: 700px;">
                  <v-card
                    v-if="vacations"
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list
                      v-for="(vocItem, index) in vacations"
                      :key="index"
                      class="pt-0 pb-3 px-0 list-group"
                      style="background-color: #fbfcfe;"
                    >
                      <v-list-item class="px-3" style="min-height: 38px">
                        <v-list-item-icon class="mr-2 my-auto">
                          <v-icon style="color: #333; font-size:20px; font-weight: 600;">mdi-update</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title style="font-size:14px;">
                            <span>{{ vocItem.extra.experience ? vocItem.extra.experience : '' }} Yillik staj</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-divider color="#DCE5EF" class></v-divider>
                      <v-list-item
                        v-if="vocItem.extra.extra_leave > 0"
                        style="min-height: 38px"
                        class="px-3"
                      >
                        <v-list-item-icon class="mr-2 my-auto list-icons">
                          <v-icon style="color: #333; font-size:20px; font-weight: 600;">mdi-disqus</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title
                            style="font-size:14px;"
                          >{{ vocItem.extra.extra_leave ? vocItem.extra.extra_leave : '' }} Kunlik qo'shimcha ta'til</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                      <v-card-text
                        class="px-4"
                        v-if="vocItem.usedVocation == 0"
                      >Foydalanilgan ta'tillar mavjud emas</v-card-text>
                      <v-card-text
                        class="px-4 pb-0"
                        v-if="vocItem.usedVocation != 0"
                      >Foydalanilgan ta'tillar</v-card-text>
                      <template v-if="vocItem.usedVocation != 0">
                        <div
                          v-for="(vocatioItem, index) in vocItem.usedVocation"
                          :key="index"
                          class="list-group px-3 pt-3"
                        >
                          <v-list-item style="min-height: 30px;">
                            <v-list-item-icon class="mr-2 my-auto">
                              <v-icon
                                style="color: #6C869F; font-size:20px; font-weight: 600;"
                              >mdi-update</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content class="py-0 list-text">
                              <v-list-item-title style="font-size:14px;">
                                {{ vocatioItem.interval1.substring(0, 4) }}~{{
                                vocatioItem.interval2.substring(0, 4)
                                }}
                                yil uchun
                                {{
                                vocationType.find(
                                (c) => c.value == vocatioItem.vocationtype
                                ).text
                                }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item style="min-height: 30px">
                            <v-list-item-icon class="mr-2 my-auto">
                              <v-icon
                                style="color: #6C869F; font-size:20px; font-weight: 600;"
                              >mdi-file-document</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content class="py-0 list-text">
                              <v-list-item-title style="font-size:14px;">{{ vocatioItem.docnum }}</v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item style="min-height: 30px">
                            <v-list-item-icon class="mr-2 my-auto">
                              <v-icon
                                style="color: #6C869F; font-size:20px; font-weight: 600;"
                              >mdi-calendar-month</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content class="py-0 list-text">
                              <v-list-item-title
                                style="font-size:14px;"
                              >{{ vocatioItem.take1 + "-" + vocatioItem.take2 }} sana oxrida</v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item style="min-height: 30px">
                            <v-list-item-icon class="mr-2 my-auto">
                              <v-icon
                                style="color: #6C869F; font-size:20px; font-weight: 600;"
                              >mdi-calendar</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content class="py-0 list-text">
                              <v-list-item-title
                                style="font-size:14px;"
                              >{{ vocatioItem.takedate }} kuni olingan</v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                        </div>
                      </template>
                    </v-list>
                  </v-card>
                </v-tab-item>
                <v-tab-item value="tab-7" class="mx-0 pa-0" style="min-height: 700px;">
                  <v-row class="mx-0" style="display: flex;">
                    <v-col sm="12" md="7">
                      <v-list class="py-2 px-3 list-group" style="display: flex;">
                        <v-btn elevation="0" style="margin-right: 10px; color: #000F30; background: #fff; height: 25px; padding: 0px 8px; letter-spacing: 0;" small class="btnCreate">
                          
                          {{ $t("Выберите год") }}
                          <v-icon size="18" style="font-weight: 900; margin: 0;" right > 
                            mdi-menu-down
                          </v-icon>
                        </v-btn>
                        <v-btn elevation="0" style="color: #000F30; background: #fff; height: 25px; padding: 0px 8px; letter-spacing: 0;" small class="btnCreate mr-10">
                          
                          {{ $t("Выберите месяц") }}
                          <v-icon size="18" style="font-weight: 900; margin: 0;" right > 
                            mdi-menu-down
                          </v-icon>
                        </v-btn>
                        <v-list-item style="min-height: 25px; margin-bottom: 5px;">
                          <v-list-item-content class="py-0 list-text" style="display: inline-block;">
                            <span style="color: #476887; font-size: 11px; font-weight: 500;">
                              {{ $t("Год:") }}
                            </span>
                            <span style="color: #000F30; font-size: 13px; margin-left: 5px;"> 2023 </span>
                          </v-list-item-content>
                        </v-list-item>
                        <v-list-item style="min-height: 25px; margin-bottom: 5px;">
                          <v-list-item-content class="py-0 list-text" style="display: inline-block;">
                            <span style="color: #476887; font-size: 11px; font-weight: 500;">
                              {{ $t("Месяц:") }}
                            </span>
                            <span style="color: #000F30; font-size: 13px; margin-left: 5px;"> Июль </span>
                          </v-list-item-content>
                        </v-list-item>
                      </v-list>
                    </v-col>
                  </v-row>
                  <v-row class="mx-0" style="display: flex; justify-content: center;">
                    <v-col sm="12" md="3">
                      <v-card>
                        <v-card-title class="material_report_title mb-2" style="display: flex; justify-content: center;">
                          Общее количество ОТВ
                        </v-card-title>

                        <v-card-subtitle class="material_report_subtitle text-center">
                          1290
                        </v-card-subtitle>
                      </v-card>                      
                    </v-col>
                    <v-col sm="12" md="3">
                      <v-card>
                        <v-card-title class="material_report_title mb-2" style="display: flex; justify-content: center;">
                          Мат отчёт сдали
                        </v-card-title>

                        <v-card-subtitle class="material_report_subtitle text-center">
                          1000
                        </v-card-subtitle>
                      </v-card>                      
                    </v-col>
                    <v-col sm="12" md="3">
                      <v-card>
                        <v-card-title class="material_report_title mb-2" style="display: flex; justify-content: center;">
                          Мат отчёт не сдали
                        </v-card-title>

                        <v-card-subtitle class="material_report_subtitle text-center">
                          290
                        </v-card-subtitle>
                      </v-card>
                    </v-col>
                  </v-row>
                  <v-card
                    elevation="0"
                    class="mt-4 pa-0"
                  >
                    <v-card-title class="ma-0 pa-0">
                      <div class="headerSearch d-flex align-center">
                        <v-text-field
                          class="txt_search1"
                          v-model="search"
                          prepend-inner-icon="mdi-magnify"
                          style="width: 100px !important"
                          :placeholder="$t('search')"
                          @keyup.native.enter="getList"
                          dense
                          hide-details
                          solo
                        ></v-text-field>
                        <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
                          <v-icon color="#00B950" left>mdi-filter-outline</v-icon>Фильтр
                        </v-btn>
                        <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
                            Столбцы <v-icon color="#00B950" right>mdi-checkbox-marked-outline</v-icon>
                        </v-btn>
                        <v-menu
                          transition="slide-y-transition"
                          left
                          
                          :close-on-content-click="false"
                          :nudge-width="50"
                          offset-y
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-btn
                              class="txt_searchBtn ml-2"
                              outlined
                              v-bind="attrs"
                              v-on="on"
                            >
                              <v-icon size="18" color="white"
                                >mdi-format-list-bulleted</v-icon
                              >
                            </v-btn>
                          </template>
                          <v-card>
                            <v-list class="dropdown-list pa-0">
                              <v-list-item
                                style="margin: 0px; max-height: 34px; min-height: 34px"
                                @click="newItem"
                              >
                                <v-list-item-title>
                                  <v-icon size="18">mdi-plus</v-icon>
                                  Добавить новую строку
                                </v-list-item-title></v-list-item
                              >
                              <v-list-item
                                style="margin: 0px; max-height: 34px; min-height: 34px"
                                @click="0"
                              >
                                <v-list-item-title
                                @click="
                                  getUserExcel(1);
                                  user_excel = [];
                                "
                                >
                                  <v-icon color="#107C41" size="18"
                                    >mdi-microsoft-excel</v-icon
                                  >
                                  Скачать таблицу Excel
                                </v-list-item-title></v-list-item
                              >
                            </v-list>
                          </v-card>
                        </v-menu> 
                      </div>
                    </v-card-title>
                    <v-row class="mx-0 mt-10">
                      <v-col class="ma-0 pa-0" xs="12">
                        <v-data-table
                          class="doc-template_data-table table_min-height"
                          dense
                          style="
                            width: 100%;
                            height:100%;
                            border-radius: 10px;
                          "
                          fixed-header
                          :loading-text="$t('loadingText')"
                          :no-data-text="$t('noDataText')"
                          :height="screenHeight"
                          :loading="loading"
                          :headers="headersStatic"
                          item-key="id"
                          :items="itemsStatic"
                          :server-items-length="server_items_length"
                          :options.sync="dataTableOptions"
                          :disable-pagination="true"
                          :footer-props="{
                            itemsPerPageOptions: [20, 50, 100, -1],
                            itemsPerPageAllText: $t('itemsPerPageAllText'),
                            itemsPerPageText: $t('itemsPerPageText'),
                            showFirstLastPage: true,
                            firstIcon: 'mdi-arrow-collapse-left',
                            lastIcon: 'mdi-arrow-collapse-right',
                            prevIcon: 'mdi-arrow-left',
                            nextIcon: 'mdi-arrow-right',
                          }"
                          @update:page="updatePage"
                          @update:items-per-page="updatePerPage"
                        > 
                          <template v-slot:item.status="{ item }">
                            <v-btn
                              v-if="item.status == 0"
                              class
                              color="#00B950"
                              right
                              small
                              dark
                              elevation="0"
                              style="min-width:75%; text-transform: none; border-radius: 10px; padding: 2px 20px; height: 20px;"
                            >
                              {{ $t("На согласовании") }}
                            </v-btn>
                            <v-btn
                              v-if="item.status == 1"
                              class
                              color="#FF0000"
                              right
                              small
                              dark
                              elevation="0"
                              style="min-width:75%; text-transform: none; border-radius: 10px; padding: 2px 20px; height: 20px;"
                            >
                              {{ $t("Не сдал") }}
                            </v-btn>
                            <v-btn
                              v-if="item.status == 2"
                              class
                              color="#F8A300"
                              right
                              small
                              dark
                              elevation="0"
                              style="min-width:75%; text-transform: none; border-radius: 10px; padding: 2px 20px; height: 20px;"
                            >
                              {{ $t("Утверждён") }}
                            </v-btn>
                          </template>                  
                          <template v-slot:item.actions="{ item }">
                            <v-btn
                              class="px-1"
                              color="#676768"
                              style="min-width: 25px"
                              small
                              text
                            >
                              <v-icon size="18">mdi-eye</v-icon>
                            </v-btn>
                          </template>
                        </v-data-table>
                      </v-col>
                    </v-row>
                  </v-card>
                </v-tab-item>
                <v-tab-item value="tab-8" class="mx-0 pa-0" style="min-height: 700px;">
                  <v-card
                    elevation="0"
                    style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;"
                  >
                    <v-list
                      class="pa-0 px-0 list-group"
                      style="background-color: #fbfcfe;"
                    >
                      <v-list-item class="px-3" style="min-height: 38px">
                        <v-list-item-content class="py-0 list-text">
                          <v-list-item-title style="font-size:14px;">
                            <span> Org Texnika </span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </v-card>
                </v-tab-item>
                <v-tab-item value="tab-" class="px-4 py-1">
                  <v-card class="pa-4 mx-3 mb-4">
                    <v-timeline>
                      <v-timeline-item v-for="(item, index) in employeeTree" :key="index" large>
                        <template v-slot:icon>
                          <v-avatar class="elevation-3">
                            <img
                              :src="
                                'https://b-edo.uzautomotors.com/get-img/' +
                                item.tabel
                              "
                              @error="setDefaultImage"
                              style="width: 60px; height: 50px"
                            />
                          </v-avatar>
                        </template>
                        <template v-slot:opposite></template>
                        <v-card class="elevation-5">
                          <div class="pa-2">
                            <h3 style="font-size: 14px; display: inline">
                              <v-icon color="blue">mdi-account</v-icon>
                              {{ item.lastname_uz_latin ? item.lastname_uz_latin : ''}}
                              {{ item.firstname_uz_latin ? item.firstname_uz_latin : ''}}
                              {{ item.middlename_uz_latin ? item.middlename_uz_latin : '' }}
                            </h3>
                            <!-- <h4 style="font-size: 13px">
                              <v-icon>mdi-bank</v-icon>
                              {{
                              item.main_staff[0].department.department_code ? item.main_staff[0].department.department_code : ''
                              }}
                              -
                              {{ item.main_staff[0].department.name_uz_latin ? item.main_staff[0].department.name_uz_latin : '' }}
                            </h4>
                            <p style="font-size: 12px">
                              <v-icon>mdi-subdirectory-arrow-right</v-icon>
                              {{ item.main_staff[0].position.name_uz_latin ? item.main_staff[0].position.name_uz_latin : '' }}
                            </p> -->
                          </div>
                        </v-card>
                      </v-timeline-item>
                    </v-timeline>
                  </v-card>
                </v-tab-item>
                <v-tab-item value="tab-" class="px-4 py-1">
                  <v-card v-if="employee_work_histories" class="pa-2 mx-3 mt-3 mb-4">
                    <v-simple-table fixed-header>
                      <template v-slot:default>
                        <thead>
                          <tr>
                            <th class="text-left">#</th>
                            <th class="text-left">{{ $t("Boshlanish vaqti") }}</th>
                            <th class="text-left">{{ $t("Tugash vaqti") }}</th>
                            <th class="text-left">{{ $t("employee.work_place") }}</th>
                            <th class="text-left">{{ $t("employee.position") }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(itmw, eWork) in employee_work_histories" :key="eWork">
                            <td class>{{ eWork + 1 }}</td>
                            <td class>{{ itmw.begin_date ? itmw.begin_date : "" }}</td>
                            <td class>{{ itmw.end_date ? itmw.end_date : "" }}</td>
                            <td class>{{ itmw.work_place ? itmw.work_place : "" }}</td>
                            <td class>{{ itmw.position ? itmw.position : "" }}</td>
                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </v-card>
                </v-tab-item>
                <v-tab-item value="tab-" class="px-4 py-1">
                  <v-card v-if="employee_relatives" class="pa-2 mx-3 mt-3 mb-4">
                    <v-simple-table fixed-header>
                      <template v-slot:default>
                        <thead>
                          <tr>
                            <th class="text-left">#</th>
                            <th class="text-left">{{ $t("employee.lastname") }}</th>
                            <th class="text-left">{{ $t("employee.firstname") }}</th>
                            <th class="text-left">{{ $t("employee.middlename") }}</th>
                            <th class="text-left">{{ $t("employee.born_date") }}</th>
                            <th class="text-left">{{ $t("employee.work_place") }}</th>
                            <th class="text-left">{{ $t("employee.living_place") }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(it, eRelative) in employee_relatives" :key="eRelative">
                            <td class>{{ eRelative + 1 }}</td>
                            <td class>{{ it.last_name ? it.last_name : "" }}</td>
                            <td class>{{ it.first_name ? it.first_name : "" }}</td>
                            <td class>{{ it.middle_name ? it.middle_name : "" }}</td>
                            <td class>{{ it.born_date ? it.born_date : "" }}</td>
                            <td class>{{ it.work_place ? it.work_place : "" }}</td>
                            <td class>{{ it.living_place ? it.living_place : "" }}</td>
                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </v-card>
                  <v-card v-else class="py-8" color="basil" flat>
                    <v-alert
                      class="d-flex justify-center rounded-0 py-5"
                      text
                      type="error"
                      icon="mdi-alert-outline"
                    >{{ $t("noDataText") }}</v-alert>
                  </v-card>
                </v-tab-item>
                <v-tab-item value="tab-" class="background_tab-4">
                  <v-card
                    v-if="historys.length"
                    class="py-8"
                    color="basil"
                    flat
                    style="background: transparent"
                  >
                    <v-row class="ma-0">
                      <v-col cols="12" md="2"></v-col>
                      <v-col cols="12" sm="6" md="8">
                        <template>
                          <v-timeline>
                            <v-timeline-item
                              v-for="(history, index) in historys"
                              :key="index"
                              small
                            >
                              <template v-slot:icon>
                                <v-avatar class="elevation-4">
                                  <img
                                    v-if="employee.base64"
                                    :src="
                                      'data:application/jpg;base64,' +
                                      employee.base64
                                    "
                                  />
                                  <img v-else src="../../assets/User-Default.jpg" />
                                </v-avatar>
                              </template>
                              <template v-slot:opposite>
                                <span>
                                  {{ history.enter_order_date }}
                                  {{ history.leave_order_date }}
                                </span>
                              </template>
                              <v-card class="elevation-2" v-if="history.staff.department">
                                <v-card-title class="headline subtitle-1 font-weight-bold pb-2">
                                  <span class="font-weight-medium mr-2">
                                    {{
                                    history.staff.department.department_code
                                    ? history.staff.department
                                    .department_code
                                    : ""
                                    }}
                                  </span>
                                  {{
                                  history.staff.department[
                                  "name_" + $i18n.locale
                                  ]
                                  }}
                                </v-card-title>
                                <v-card-text
                                  class="subtitle-2 font-weight-medium"
                                  v-if="history.staff.position"
                                >
                                  {{
                                  history.staff.position[
                                  "name_" + $i18n.locale
                                  ]
                                  }}
                                </v-card-text>
                              </v-card>
                            </v-timeline-item>
                          </v-timeline>
                        </template>
                      </v-col>
                      <v-col cols="12" md="2"></v-col>
                    </v-row>
                  </v-card>
                  <v-card
                    v-else
                    class="py-8"
                    color="basil"
                    flat
                    style="
                      background: transparent;
                      color: red;
                      text-align: center;
                    "
                  >{{ $t("noDataText") }}</v-card>
                </v-tab-item>
                <v-tab-item value="tab-" class="px-4 py-1">
                  <v-card v-if="employee_education_histories" class="pa-2 mx-3 mt-3 mb-4">
                    <v-simple-table fixed-header>
                      <template v-slot:default>
                        <thead>
                          <tr>
                            <th class="text-left">#</th>
                            <th class="text-left">{{ $t("university") }}</th>
                            <th class="text-left">{{ $t("study_type") }}</th>
                            <th class="text-left">{{ $t("major") }}</th>
                            <th class="text-left">{{ $t("start_date") }}</th>
                            <th class="text-left">{{ $t("end_date") }}</th>
                            <th class="text-left">{{ $t("university_address") }}</th>
                            <th class="text-left">{{ $t("academic_title") }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(itme, eEdu) in employee_education_histories" :key="eEdu">
                            <td class>{{ eEdu + 1 }}</td>
                            <td class>
                              {{
                              itme.university
                              ? itme.university["name_" + $i18n.locale]
                              : ""
                              }}
                            </td>
                            <td class>
                              {{
                              itme.study_type
                              ? itme.study_type["name_" + $i18n.locale]
                              : ""
                              }}
                            </td>
                            <td class>
                              {{
                              itme.major
                              ? itme.major["name_" + $i18n.locale]
                              : ""
                              }}
                            </td>
                            <td class>{{ itme.begin_date ? itme.begin_date : "" }}</td>
                            <td class>{{ itme.end_date ? itme.end_date : "" }}</td>
                            <td class>
                              {{
                              itme.university_address
                              ? itme.university_address
                              : ""
                              }}
                            </td>
                            <td class>
                              {{
                              itme.academic_title ? itme.academic_title : ""
                              }}
                            </td>
                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </v-card>
                </v-tab-item>
              </v-tabs-items>
            </v-col>
          </v-row>
        </v-col>
        <v-col sm="12" md="3" class="pl-0 pr-5 pr-0" style="background: #fff;">
          <div>
            <v-card class elevation="0" style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;">
              <v-list class="py-2 px-3 list-group">
                <!-- <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon>mdi-account-box-outline</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      {{ $t("profile.tabel") }}
                      <span>{{ employee.tabel ? employee.tabel : '' }}</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item> -->
                <v-list-item v-if="employee.user !== null" style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon>mdi-email-outline</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <!-- {{ $t("profile.email") }} -->
                      <span v-if="employee.user && employee.user.email" :title="employee.user.email">
                        {{
                        employee.user && employee.user.email
                        ? employee.user.email
                        : ""
                        }}
                      </span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
                <!-- <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon>mdi-clock-fast</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      {{ $t("profile.first_work_date") }}
                      <span>
                        {{
                        employee.first_work_date ? employee.first_work_date : ""
                        }}
                      </span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item> -->
                <!-- <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon>mdi-file-tree</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      {{ $t("profile.department") }}
                      <span v-if="staff.department && staff.department.name_uz_latin" :title="staff.department.name_uz_latin">{{ staff.department.name_uz_latin ? staff.department.name_uz_latin : '' }}</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item> -->
                <!-- <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon>mdi-briefcase-outline</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      {{ $t("profile.employee_position") }}
                      <span>{{ staff.position.name_uz_latin ? staff.position.name_uz_latin : ''}}</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item> -->
                <v-list-item
                  v-if="employee_phones.length>=0"
                  style="min-height: 38px"
                  v-for="(employee_phone, index) in employee_phones"
                  :key="'employee_phone' + index"
                >
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon>mdi-cellphone</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <!-- {{ $t("profile.phone_number") }} -->
                      <span>
                        {{
                        employee_phone.phone_number
                        ? employee_phone.phone_number
                        : ""
                        }}
                      </span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
              <v-divider style="color: #DCE5EF; border-color: #DCE5EF;"></v-divider>
              <v-list class="py-2 px-3 list-group">
                <v-list-item style="min-height: 38px; margin-bottom: 5px;">
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title style="color: #476887; font-size: 11px;">
                      {{ $t("Дата найма") }}
                    </v-list-item-title>
                    <span style="color: #000F30; font-size: 13px;"> 3 Окт. 2023 </span>
                  </v-list-item-content>
                </v-list-item>
                <v-list-item style="min-height: 38px; margin-bottom: 5px;">
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title style="color: #476887; font-size: 11px;">
                      {{ $t("Срок работы") }}
                    </v-list-item-title>
                    <span style="color: #000F30; font-size: 13px;"> Сегодня </span>
                  </v-list-item-content>
                </v-list-item>
                <v-list-item style="min-height: 38px; margin-bottom: 5px;">
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title style="color: #476887; font-size: 11px;">
                      {{ $t("Последняя активность") }}
                    </v-list-item-title>
                    <span style="color: #000F30; font-size: 13px;"> 03 Окт. 2023, 09:03 </span>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
            <v-card class="mt-4" elevation="0" style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;">
              <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon>mdi-cellphone</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span>{{ $t("profile.supervisor") }}</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
              <v-divider color="#DCE5EF" class></v-divider>
              <v-list v-if="supervisor.tabel > 0" class="py-0 px-3 list-group">
                <v-list-item>
                  <v-list-item-avatar>
                    <img
                      :src="
                        'https://b-edo.uzautomotors.com/get-img/' +
                        supervisor.tabel
                      "
                      @error="setDefaultImage"
                      style="width: 60px; height: 50px"
                    />
                  </v-list-item-avatar>
                  <v-list-item-content>
                    <v-list-item-title>
                      <h3 style="font-size: 14px; display: inline">
                        {{ supervisor.lastname_uz_latin ? supervisor.lastname_uz_latin : '' }}
                        {{ supervisor.firstname_uz_latin ? supervisor.firstname_uz_latin : '' }}
                        {{ supervisor.middlename_uz_latin ? supervisor.middlename_uz_latin : '' }}
                      </h3>
                    </v-list-item-title>
                    <!-- <v-list-item-subtitle
                      :title="supervisor.main_staff[0].position.name_uz_latin ? supervisor.main_staff[0].position.name_uz_latin : ''"
                    >
                      <p
                        style="font-size: 13px; margin-bottom:0;"
                      >{{ supervisor.main_staff[0].position.name_uz_latin ? supervisor.main_staff[0].position.name_uz_latin : '' }}</p>
                    </v-list-item-subtitle> -->
                  </v-list-item-content>
                </v-list-item>
              </v-list>
              <v-list v-else class="py-0 px-3 list-group">
                <v-list-item class="list-group" style="min-height: 38px">
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>{{ $t("profile.noSupervisor") }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
            <v-card class="mt-4" elevation="0" style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;">
              <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon>mdi-cellphone</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span>{{ $t("profile.subordinates") }}</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
              <v-divider color="#DCE5EF" class></v-divider>
              <v-list v-if="boysunuvchilar[2] && boysunuvchilar[2].length>0" class="py-0 px-3 list-group">
                <v-list-item
                  v-for="(subor, dinate) in boysunuvchilar[2]"
                  :key="dinate"
                  class="list-group"
                  style="min-height: 38px"
                >
                  <v-list-item-avatar>
                    <img
                      :src="
                        'https://b-edo.uzautomotors.com/get-img/' +
                        subor.tabel
                      "
                      @error="setDefaultImage"
                      style="width: 60px; height: 50px"
                    />
                  </v-list-item-avatar>
                  <v-list-item-content class="py-0">
                    <v-list-item-title>
                      <h3 style="font-size: 14px; display: inline">
                        {{ subor.lastname_uz_latin ? subor.lastname_uz_latin : '' }}
                        {{ subor.firstname_uz_latin ? subor.firstname_uz_latin : '' }}
                        {{ subor.middlename_uz_latin ? subor.middlename_uz_latin : '' }}
                      </h3>
                    </v-list-item-title>
                    <!-- <v-list-item-subtitle>
                      <p
                        style="font-size: 13px; margin-bottom:0;"
                      >{{ subor.main_staff[0].position.name_uz_latin ? subor.main_staff[0].position.name_uz_latin : '' }}</p>
                    </v-list-item-subtitle> -->
                  </v-list-item-content>
                </v-list-item>
              </v-list>
              <v-list v-else class="py-0 px-3 list-group">
                <v-list-item class="list-group" style="min-height: 38px">
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>{{ $t("profile.noSubordinates") }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
            <v-card class="mt-4" elevation="0" style="border-radius:10px; border: 1px solid #dce5ef; background-color: #fffffa;">
              <v-list class="py-0 px-3 list-group" style="background-color: #fbfcfe;">
                <v-list-item style="min-height: 38px">
                  <v-list-item-icon class="mr-2 my-auto list-icons">
                    <v-icon>mdi-cellphone</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title>
                      <span>{{ $t("profile.teams") }}</span>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
              <v-divider color="#DCE5EF" class></v-divider>
              <v-list class="py-0 px-3 list-group">
                <v-list-item class="list-group" style="min-height: 38px">
                  <v-list-item-content class="py-0 list-text">
                    <v-list-item-title> {{ $t("profile.noTeam") }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
          </div>
        </v-col>
        <v-col sm="12" md="12">
        </v-col>
      </v-row>
    </div>
    <v-dialog
      v-model="eImzoDialog"
      scrollable
      persistent
      @keydown.esc="eImzoDialog = false"
      :overlay="false"
      max-width="650px"
      transition="dialog-transition"
    >
      <v-card style="height: 100%; border-radius:10px; background-color: #fffffa;">
        <v-card-title class="headline grey lighten-2 py-3" primary-title>
          {{ $t("profile.choose_key") }}
          <v-spacer></v-spacer>
          <v-btn color="red" dark x-small fab class @click="eImzoDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-row class="ma-0">
          <v-col cols="12" md="12" class="pa-4">
            <v-card-text class="pa-0">
              <label v-show="false" id="message"></label>
              <v-form name="testform">
                <v-row>
                  <v-col cols="12" class="my-0 py-0">
                    {{ $t("profile.select_key") }}
                    <br />
                    <select
                      name="key"
                      @change="cbChanged(this)"
                      style="border: 1px solid black"
                      class="pa-2 v-input__control"
                    ></select>
                    <br />
                    <label v-show="false" id="keyId"></label>
                  </v-col>
                  <v-col cols="12" class="mt-0 mb-6 py-0">
                    <v-btn block color="#203d5b" dark @click="push()">
                      {{ $t("profile.push") }}
                      <v-progress-circular v-if="loading" indeterminate :width="3" :size="18"></v-progress-circular>
                    </v-btn>
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>
          </v-col>
        </v-row>
        <v-card-actions>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="roleDialog"
      scrollable
      persistent
      @keydown.esc="roleDialog = false"
      :overlay="false"
      max-width="650px"
      transition="dialog-transition"
    >
      <v-card style="height: 100%; border-radius:10px; background-color: #fffffa;">
        <v-card-title class="headline grey lighten-2 py-3" primary-title>
          {{ $t("user.roles") }}
          <v-spacer></v-spacer>
          <v-btn color="red" dark x-small fab class @click="roleDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-row class="ma-0">
          <v-col cols="12" md="12" class="pa-4">
            <v-card-text class="pa-0" v-if="roles.length >= 0">
              <span
                v-for="(role, i) in roles"
                :key="i"
                class="font-weight-bold"
                style="color: #444;"
              >
                <v-chip
                  style="color: #476887;"
                  color="white"
                  class="pa-1 ma-0"
                >{{ role.name + " , " + " " }}</v-chip>
              </span>
            </v-card-text>
          </v-col>
        </v-row>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogTask" @keydown.esc="dialogTask = false" persistent max-width="800px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ dialogHeaderText }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialogTask = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="ma-0 pa-0">
              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("profile.task_name") }}</label>
                <v-text-field
                  v-model="form.task"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("profile.begin_date") }}</label>
                <v-menu
                  v-model="createdAtMenu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="form.begin_date"
                      :rules="[(v) => !!v || $t('input.required')]"
                      readonly
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="form.begin_date" @input="createdAtMenu = false"></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("profile.end_date") }}</label>
                <v-menu
                  v-model="createdAtMenu1"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="form.due_date"
                      :rules="[(v) => !!v || $t('input.required')]"
                      readonly
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="form.due_date" @input="createdAtMenu1 = false"></v-date-picker>
                </v-menu>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="dialogTaskComment"
      @keydown.esc="dialogTaskComment = false"
      persistent
      max-width="600"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ dialogHeaderText }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialogTaskComment = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="saveTaskComment">
            <v-row>
              <v-col cols="12">
                <label for>{{ $t("profile.description") }}</label>
                <v-text-field
                  v-model="form.description"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col
                cols="12"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <label for>{{ $t("uploadFiles") }}</label>
                <v-file-input
                  v-model="file"
                  :rules="[
                    (v) => {
                      let allowedExtensions = /(\.pdf)$/i;
                      let error = false;
                      (v) => {
                        if (!allowedExtensions.exec(file.name)) {
                          error = true;
                        }
                      };
                      return !error || $t('requiredpdf');
                    },
                    (v) => !!v || $t('input.required'),
                  ]"
                  outlined
                  dense
                  prepend-icon
                  append-icon="mdi-file-pdf-box-outline"
                  accept=".pdf, application/pdf"
                  small-chips
                  show-size
                  hide-details="auto"
                ></v-file-input>
              </v-col>
            </v-row>
          </v-form>
          <small color="red">{{ $t("input_required") }}</small>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="saveTaskComment">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="pdfViewDialog" fullscreen>
      <v-card>
        <v-card-title primary-title>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined @click="pdfViewDialog = false">{{ $t("close") }}</v-btn>
        </v-card-title>
        <v-card-text class="d-flex justify-center">
          <iframe
            width="100%"
            :height="830"
            :src="$store.state.backend_url + 'staffs/get-file/' + fileForView"
          ></iframe>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined @click="pdfViewDialog = false" class="mr-4">{{ $t("close") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      user: null,
      eImzoDialog: false,
      base64: "",
      employee: {
        base64: null
      },
      pdfBase64: null,
      staff: {},
      vacations: {},
      roles: [],
      employee_addresses: [],
      employee_coefficients: [],
      employee_official_document: [],
      employee_phones: [],
      employee_relatives: [],
      employee_work_histories: [],
      employee_education_histories: [],
      employee_id: "",
      employee_tabel: "",
      dialogHeaderText: "",
      tariff_scale: {},
      nationality: {},
      company: {},
      historys: [],
      allDocuments: [],
      form: {},
      employeeTasks: [],
      employeeTree: [],
      boysunuvchilar: [],
      subordinates: [],
      supervisor: [],
      dialogTask: false,
      selectFiles: [],
      file: null,
      documentFiles: [],
      formData: [],
      dialogTaskComment: false,
      fileForView: null,
      myTable: null,
      pdfViewDialog: false,
      singleExpand: false,
      expanded: [],
      items: [],
      shepi: null,
      createdAtMenu: false,
      createdAtMenu1: false,
      loading: false,
      tab: null,
      eimzo_username: "",
      eimzo_name: "",
      eimzo_password: "",
      eimzo_inn: "",
      eimzo_given_date: "",
      eimzo_expere_date: "",
      roleDialog: false,
      vocationType: [
        { value: "T", text: "Mexnat ta'tili" },
        { value: "S", text: "Qo'shimcha ta'til yil uchun" },
        { value: "D", text: "Qo'shimcha ta'til" },
        { value: "M", text: "Ommaviy ta'til" }
      ],
      document_status: [
        {
          id: 0,
          name_uz_latin: "Yangi",
          name_uz_cyril: "Янги",
          name_ru: "Новый"
        },
        {
          id: 1,
          name_uz_latin: "E'lon qilindi",
          name_uz_cyril: "Эьлон ?илинди",
          name_ru: "Опубликованный"
        },
        {
          id: 2,
          name_uz_latin: "Qayta ishlash",
          name_uz_cyril: "?айта ишлаш",
          name_ru: "Обработка"
        },
        {
          id: 3,
          name_uz_latin: "Imzolandi",
          name_uz_cyril: "Имзоланди",
          name_ru: "Подписано"
        },
        {
          id: 4,
          name_uz_latin: "Bajarildi",
          name_uz_cyril: "Бажарилди",
          name_ru: "Выполнено"
        },
        {
          id: 5,
          name_uz_latin: "Yakunlandi",
          name_uz_cyril: "Якунланди",
          name_ru: "Завершено"
        },
        {
          id: 6,
          name_uz_latin: "Bekor qilindi",
          name_uz_cyril: "Бекор ?илинди",
          name_ru: "Отменен"
        }
      ],
      itemsStatic: [
        {
          id: 1,
          test: "L440",
          test1: "Юсупов",
          test2: "Управление Инженерной продукции",
          test3: "MAT2023-08-L440",
          status: 0,
          actions: '',
        },
        {
          id: 2,
          test: "L440",
          test1: "Юсупов",
          test2: "Управление Инженерной продукции",
          test3: "MAT2023-08-L440",
          status: 1,
          actions: '',
        },
        {
          id: 3,
          test: "L440",
          test1: "Юсупов",
          test2: "Управление Инженерной продукции",
          test3: "MAT2023-08-L440",
          status: 2,
          actions: '',
        },
        {
          id: 4,
          test: "L440",
          test1: "Юсупов",
          test2: "Управление Инженерной продукции",
          test3: "MAT2023-08-L440",
          status: 0,
          actions: '',
        },
      ]
    };
  },
  computed: {
    // screenHeight() {
    //   return window.innerHeight - 175;
    // },
    headersStatic() {
      return [
        { text: "#", value: "id", width: 30 },
        {
          text: this.$t("Номер склада"),
          value: "test"
        },
        {
          text: this.$t("Мат Отв.Лицо"),
          value: "test1"
        },
        {
          text: this.$t("Управление"),
          value: "test2"
        },
        {
          text: this.$t("Номер документа"),
          value: "test3"
        },
        {
          text: this.$t("Status"),
          value: "status"
        },
        {
          text: this.$t(""),
          value: "actions"
        },
      ]
    },
    headers() {
      return [
        {
          text: "",
          value: "data-table-expand",
          width: 30
        },
        { text: "#", value: "id", width: 30 },
        {
          text: this.$t("profile.task_name"),
          value: "task"
        },
        { text: this.$t("profile.begin_date"), value: "begin_date" },
        { text: this.$t("profile.end_date"), value: "due_date" },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 180,
          align: "center"
        }
      ].filter(
        v =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("critical-update") ||
          this.$store.getters.checkPermission("critical-delete")
      );
    }
  },
  methods: {
    viewFile(file_id) {
      this.fileForView = file_id;
      this.pdfViewDialog = true;
    },

    getUser(id) {
      axios
        .get(
          this.$store.state.backend_url + "api/employees/show-employee/" + id
        )
        .then(res => {
          this.employee = res.data;
          this.nationality = res.data.nationality;
          this.company = res.data.company;
          this.employee_addresses = res.data.employee_addresses;
          this.employee_official_document = res.data.employee_official_document;
          this.employee_phones = res.data.employee_phones;
          this.staff = res.data.staff[0];
          this.tariff_scale = res.data.employee_staff[0].tariff_scale;
          this.roles = res.data.user && res.data.user.roles ? res.data.user.roles : [];
          this.employee_relatives = res.data.employee_relative;
          this.employee_work_histories = res.data.employee_work_histories;
          this.employee_education_histories = res.data.employee_education_histories;
          this.employee_id = res.data.user ? res.data.user.employee_id : 0;
          this.employee_tabel = res.data.tabel;
          this.myTable = res.data.tabel;
          this.staffHistory(this.employee_id);
          console.log(this.staff);
        })

        .catch(e => {
          console.error(e);
        });
    },
    getAvatar(id) {
      axios
        .get(this.$store.state.backend_url + "api/employees/get-avatar/" + id)
        .then(response => {
          this.employee.base64 = response.data;
          this.base64 = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getUserDocument() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/user-documents/" +
            this.$route.params.id
        )
        .then(res => {
          this.allDocuments = res.data;
        })
        .catch(e => {
          console.error(e);
        });
    },
    setDefaultImage(event) {
      event.target.src = require("../../assets/User-Default.jpg");
    },
    getPositionTree() {
      let user = this.$store.getters.getUser();
      let tabel;
      if (this.selected_employee) {
        tabel = this.selected_employee;
      } else {
        tabel = user.employee.tabel;
      }
      const payload = {
        tabel: tabel
      };
      axios
        .post(
          this.$store.state.backend_url + "api/find-department-by-user",
          payload
        )
        .then(response => {
          this.employeeTree = response.data;
          if (this.employeeTree.length >= 2) {
            this.shepi = this.employeeTree[this.employeeTree.length - 2];
          } else {
            this.shepi = null;
          }
        })
        .catch(error => {
          console.error(error);
        });
    },
    getBoysunuvchilar(id) {
      axios
        .post(this.$store.state.backend_url + "api/departments/employeesnew/", {
          id: id
        })
        .then(response => {
          this.boysunuvchilar = response.data;
          this.supervisor = response.data[1];
          this.subordinates = response.data[2];
        })
        .catch(error => {
          console.error(error);
        });
    },
    getEmployeeTask() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/employee-traning-tasks/" +
            this.$route.params.id
        )
        .then(response => {
          this.items = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    newEmployeeTask() {
      this.dialogTask = true;
      this.dialogHeaderText = this.$t("profile.add_task");
      this.form = {
        id: Date.now(),
        employee_id: this.employee_id,
        task: "",
        begin_date: "",
        due_date: ""
      };

      this.editMode = false;
    },
    editEmployeeTask(item) {
      this.dialogHeaderText = this.$t("Tahrirlash");
      this.form = Object.assign({}, item);
      this.dialogTask = true;
      this.editMode = true;
    },
    save() {
      axios
        .post(
          this.$store.state.backend_url + "api/employee-traning-tasks/update",
          this.form
        )
        .then(res => {
          this.getEmployeeTask();
          this.dialogTask = false;
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            onOpen: toast => {
              toast.addEventListener("mouseenter", Swal.stopTimer);
              toast.addEventListener("mouseleave", Swal.resumeTimer);
            }
          });

          Toast.fire({
            icon: "success",
            title: this.$t("create_update_operation")
          });
        })
        .catch(err => {
          console.log(err);
        });
    },
    deleteEmployeeTask(item) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete")
      }).then(result => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url +
                "api/employee-traning-tasks/delete/" +
                item.id
            )
            .then(res => {
              this.getEmployeeTask();
              this.dialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch(err => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text")
              });
              console.log(err);
            });
        }
      });
    },
    newTaskComment(item) {
      this.dialogHeaderText = this.$t("Topshiriq bo'yicha qilingan ishlar");
      this.form = {
        id: Date.now(),
        employee_traning_task_id: item,
        description: ""
      };
      this.dialogTaskComment = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    saveTaskComment() {
      this.formData = new FormData();
      this.loading = true;
      this.formData.append("file", this.file);
      this.formData.append("id", this.form.id);
      this.formData.append(
        "employee_traning_task_id",
        this.form.employee_traning_task_id
      );
      this.formData.append("description", this.form.description);
      axios
        .post(
          this.$store.state.backend_url + "api/task-comment/update",
          this.formData,
          {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          }
        )
        .then(res => {
          this.loading = false;
          this.dialogTaskComment = false;
          this.getEmployeeTask();
          this.getList();
        })
        .catch(err => {
          console.log("err =" + err);
          this.loading = false;
        });
    },
    deleteTaskComment(item) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete")
      }).then(result => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url +
                "api/task-comment/delete/" +
                item.id
            )
            .then(res => {
              this.getEmployeeTask();
              this.dialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch(err => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text")
                //footer: "<a href>Why do I have this issue?</a>"
              });
              console.log(err);
            });
        }
      });
    },
    staffHistory(employee_id) {
      // console.log(this.employee_id);
      axios
        .post(this.$store.state.backend_url + "api/employe-staff/history", {
          employee_id: employee_id
        })
        .then(res => {
          this.historys = res.data;
        })
        .catch(err => {
          console.log(err);
        });
    },
    cbChanged(c) {
      if (document.getElementById("keyId"))
        document.getElementById("keyId").innerHTML = "";
      this.getUserAuth();
    },
    getVacations() {
      let user = this.$store.getters.getUser();
      let tabel;
      if (this.selected_employee) {
        tabel = this.selected_employee;
      } else {
        tabel = user.employee.tabel;
      }
      this.loading = true;
      this.test = {
        tabel: [tabel],
        month1: "2022",
        month2: "2023"
      };
      axios
        .post(
          this.$store.state.backend_url +
            "api/document-templates/used-vacation-days",
          { filter: this.test }
        )
        .then(response => {
          if (response != 2) {
            this.vacations = response.data;
            this.loading = false;
          } else {
            this.vacations = null;
          }
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    getUserAuth() {
      var itm = document.testform.key.value;
      var id = document.getElementById(itm);
      if (id && id.hasAttribute("vo")) {
        var vo = JSON.parse(id.getAttribute("vo"));
        this.eimzo_username = vo.name;
        this.eimzo_name = vo.CN;
        this.eimzo_password = vo.serialNumber;
        this.eimzo_inn = vo.TIN;
        this.eimzo_given_date = vo.validFrom;
        this.eimzo_expere_date = vo.validTo;
      } else {
        this.eimzo_username = "";
        this.eimzo_name = "";
        this.eimzo_password = "";
        this.eimzo_inn = "";
        this.eimzo_given_date = "";
        this.eimzo_expere_date = "";
      }
    },
    push() {
      axios
        .post(this.$store.state.backend_url + "api/users/eimzo-push", {
          eimzo_username: this.eimzo_username,
          eimzo_name: this.eimzo_name,
          eimzo_password: this.eimzo_password,
          eimzo_inn: this.eimzo_inn,
          eimzo_given_date: this.eimzo_given_date,
          eimzo_expere_date: this.eimzo_expere_date
        })
        .then(res => {
          Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Your E-IMZO has been saved",
            showConfirmButton: false,
            timer: 1500
          });
        })
        .catch(err => {});
      this.eImzoDialog = false;
    },
    AppLoad() {
      // EIMZOClient.API_KEYS = [
      //   "edo.uzautomotors.com",
      //   "79DC56F42765A0017C31309DB9600EA924684ED023A8079460454768331626AB94CFFF8FC2D4007976D4A6C56F11D56DFA962276DC54AE8C0F39E8A8EBDFA10B"
      // ];
      EIMZOClient.API_KEYS = [
        this.$store.state.EIMZO_DOMAIN,
        this.$store.state.EIMZO_API_KEY
      ];
      this.uiLoading();
      let EIMZO_MAJOR = this.EIMZO_MAJOR;
      let EIMZO_MINOR = this.EIMZO_MINOR;
      let uiLoadKeys = this.uiLoadKeys;
      EIMZOClient.checkVersion(
        function(major, minor) {
          var newVersion = EIMZO_MAJOR * 100 + EIMZO_MINOR;
          var installedVersion = parseInt(major) * 100 + parseInt(minor);
          if (installedVersion < newVersion) {
            this.uiUpdateApp();
          } else {
            EIMZOClient.installApiKeys(
              function() {
                uiLoadKeys();
              },
              function(e, r) {
                if (r) {
                  this.uiShowMessage(r);
                } else {
                  this.wsError(e);
                }
              }
            );
          }
        },
        function(e, r) {
          if (r) {
            this.uiShowMessage(r);
          } else {
            this.uiNotLoaded(e);
          }
        }
      );
    },
    uiLoading() {
      var l = document.getElementById("message");
      if (l) {
        l.innerHTML = "Загрузка ...";
        l.style.color = "red";
      }
    },
    uiClearCombo() {
      var combo = document.testform.key;
      combo.length = 0;
    },
    uiCreateItem(itmkey, vo) {
      var now = new Date();
      vo.expired = dates.compare(now, vo.validTo) > 0;
      var itm = document.createElement("option");
      itm.value = itmkey;
      itm.text = vo.CN;
      if (!vo.expired) {
      } else {
        itm.style.color = "gray";
        itm.text = itm.text + " (срок истек)";
      }
      itm.setAttribute("vo", JSON.stringify(vo));
      itm.setAttribute("id", itmkey);
      return itm;
    },
    uiUpdateApp() {
      var l = document.getElementById("message");
      if (l) l.innerHTML = this.errorUpdateApp;
    },
    uiLoadKeys() {
      this.uiClearCombo();
      let uiCreateItem = this.uiCreateItem;
      let uiShowMessage = this.uiShowMessage;
      let eimzo_password = this.employee.eimzo_password;
      let getUserAuth = this.getUserAuth;
      EIMZOClient.listAllUserKeys(
        function(o, i) {
          var itemId = "itm-" + o.serialNumber + "-" + i;
          return itemId;
        },
        function(itemId, v) {
          return uiCreateItem(itemId, v);
        },
        function(items, firstId) {
          var combo = document.testform.key;
          var option = document.createElement("option");
          option.text = "select";
          combo.add(option);
          // combo.append(<option value="">Select</option>);
          for (var itm in items) {
            var vo = items[itm].getAttribute("vo");
            if (!JSON.parse(vo).expired) {
              combo.append(items[itm]);
            }
            if (vo.includes(eimzo_password)) {
              items[itm].setAttribute("selected", "true");
              getUserAuth();
            }
          }
          // if (firstId) {
          //   var id = document.getElementById(firstId);
          //   id.setAttribute("selected", "true");
          // }
        },
        function(e, r) {
          // uiShowMessage(this.errorCAPIWS);
        }
      );
    },
    uiShowMessage(message) {
      alert(message);
    },
    wsError(e) {
      if (e) {
        this.uiShowMessage(this.errorCAPIWS + " : " + e);
      } else {
        this.uiShowMessage(this.errorBrowserWS);
      }
    },
    uiNotLoaded(e) {
      var l = document.getElementById("message");
      if (l) {
        l.innerHTML = "";
        if (e) {
          this.wsError(e);
        } else {
          this.uiShowMessage(this.errorBrowserWS);
        }
      }
    }
  },
  watch: {
    eImzoDialog(value) {
      if (value) this.AppLoad();
    }
  },
  mounted() {
    this.user = JSON.parse(window.localStorage.getItem("user"));
    if (this.$route.params.id) {
      this.getUser(this.$route.params.id);
      this.getAvatar(this.$route.params.id);
      axios
        .get(
          this.$store.state.backend_url +
            "api/employees/get-pdf/" +
            this.$route.params.id +
            "/" +
            this.$i18n.locale
        )
        .then(res => {
          this.pdfBase64 = res.data;
        })
        .catch(e => {
          console.error(e);
        });
    }
    this.getUserDocument();
    this.getEmployeeTask();
    this.getPositionTree();
    this.getBoysunuvchilar(this.$route.params.id);
    this.getVacations();
    this.staffHistory();
  }
};
</script>
<style scoped>
.background_ffa {
  background-color: #fffffa;
}
.relative {
  position: relative;
}
.profile_header {
  background-image: url("../../assets/bgoverlyy.jpg");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  background-color: rgba(0, 0, 0, 0.5);
}
.editCoverButton {
  position: absolute;
  right: 0;
  bottom: 0;
  text-transform: none;
  text-align: center;
  width: auto;
  line-height: 1.4em;
  cursor: pointer;
  font-weight: 600;
  font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 12px;
  color: #474747;
}
.profile-header_title {
  color: #fff !important;
  font-size: 22px;
  text-transform: uppercase;
  font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
  text-align: center;
  letter-spacing: 0.8px;
}
.profile_header_bottom {
  background-color: #f9f9f9;
}
.profile_name {
  color: #333;
  font-size: 22px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-weight: 600;
  word-wrap: break-word;
}
.profile_role {
  color: #006cff !important;
  font-size: 12px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-weight: 500;
  word-wrap: break-word;
}
.profile_staff {
  color: #333 !important;
  font-size: 13px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.profile_staff_icons {
  font-size: 16px;
  color: #333;
}
.text-truncate {
  color: #fff !important;
  font-size: 18px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  text-align: center;
}
.list-group .list-icons i {
  color: #00b950;
  font-size: 16px;
}
.list-group .list-text div {
  color: #6c869f;
  font-size: 13px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.list-group .list-text span {
  color: #333;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.profileTab a {
  color: #00b950;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: none;
}
.btnSave {
  color: white;
  font-size: 10px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-weight: 500;
  word-wrap: break-word;
  text-transform: none;
}
.btnEditphoto {
  color: #476887;
  background: #fff;
  border-color: #D3DFEB;
  font-size: 12px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-weight: 500;  
  text-transform: none;
  letter-spacing: none;
}
.btnNext {
  color: #000;
  background: #fff;
  border-color: #D3DFEB;
  font-size: 12px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-weight: 500;  
  text-transform: none;
  letter-spacing: none;
  border: 1px solid #eee;
}
.btnCreate {
  color: #476887;
  background: #fff;
  border-color: #476887;
  font-size: 12px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-weight: 600;  
  text-transform: none;
  letter-spacing: none;
  border: 1px solid #476887;
}
.underLineNone {
  display: none;
}
.fullHeight {
  height: 100%;
}
.border_left {
  border-left: 3px solid #009688;
  border-radius: 0px;
  background: #f7f7f7;
}
.text-color {
  color: rgba(0, 0, 0, 0.87);
}
.background_tab-3 {
  background-image: url("../../assets/bg.jpg");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  height: calc(100vh - 288px);
  border-radius: 0;
}
.background_tab-4 {
  background-image: url("../../assets/background.jpg");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  height: calc(100vh - 288px);
  border-radius: 0;
}
.background-overlay {
  /* background-color: rgba(0, 0, 0, 0.1); */
}
.material_report_title {
  color: #000;
  font-size: 15px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.material_report_subtitle {
  color: #000!important;
  font-size: 24px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 100%;
  background: #fff;
}
.headerTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialogTitle {
  color: #000;
  font-size: 16px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.labelTitle {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.headerSearch {
  width: 100%;
  height: 34px;
}
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 5px 0px 0px 5px;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_searchBtn {
  background: #ff9f0e;
  border: 0.2px rgba(0, 0, 0, 0.28) solid;
  box-shadow: none;
  min-width: 25px !important;
  height: 34px !important;
  border-radius: 1px;
  width: 25px;
  padding: 0 13px;
}
.filterBtn {
  color: #000;
  font-size: 12px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  border: 1px solid #e6e6e6;
  /* border-right: 0px; */
  border-left: 0px;
  background: #fff;
  box-shadow: none;
  border-radius: 0;
  text-transform: none;
}
.v-data-table {
  line-height: 13px !important;
}
.doc-template_data-table > .v-data-table__wrapper > table > tbody > tr > td {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dropdown-list .v-list-item .v-list-item__title {
  color: #000;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialog-head_title {
  color: #000;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.v-dialog > .v-card > .v-card__text {
  padding: 0px 0px 0px 0px;
}
.doc-template_data-table table > tbody > tr > td {
  white-space: normal;
  max-width: 50px;
  height: 43px;
  margin: 0 auto;
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
