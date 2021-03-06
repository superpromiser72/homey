<template>
    <div class="container-fluid bg-light-gray m-0 p-0 pb-5">
        <div class="container m-0 p-0 mx-auto advice-to-customers mt-5 mb-3 box-block">
            <div class="p-3 pb-5 pt-2">
                <h2 class="p-3">{{ $t('community').UpdateTopic }}</h2>
                <Form :model="updateCommunityData">
                    <div class="row m-0 p-0">
                        <div class="col-12 mb-3 gray-input fac-name">
                            <Input v-model="updateCommunityData.title" :placeholder="$t('placeholder').enterTitle" />
                        </div>
                        <div class="col-12 mb-3">
                            <p class="">Period</p>
                            <RadioGroup @on-change="setPeriodToBlank" v-model="periodType" class="">
                                <Radio label="withPeriod">{{ $t('community').ChoosePeriod }}</Radio>
                                <Radio label="withCalendar">{{ $t('community').ChooseCalendar }}</Radio>
                            </RadioGroup>   
                            <DatePicker v-if="periodType == 'withCalendar'" class="w-50 mx-auto" :options="disableBeforeDate" @on-change="notiDateChange" style="display: block" type="datetimerange" :placeholder="$t('placeholder').selectPeriod"></DatePicker>
                            <RadioGroup v-else v-model="initPeriod" class=" w-50 mx-auto d-flex justify-content-between">
                                <Radio label="forever">{{ $t('community').Forever }}</Radio>
                                <Radio label="week">{{ $t('community').OneWeek }}</Radio>
                                <Radio label="month">{{ $t('community').OneMonth }}</Radio>
                            </RadioGroup> 
                        </div>
                        <div class="col-12 mb-3">
                            <wysiwyg v-model="updateCommunityData.content" :placeholder="$t('placeholder').enterDescription"/>
                        </div>
                        <div class="col-12 text-left d-flex justify-content-start mt-3 position-relative">
                            <Upload
                                @upImgUrl="upImgUrl"
                                @upFileUrl="upFileUrl"
                                @upVideoUrl="upVideoUrl"
                            />
                            <Icon @click="toggleEmo" class="pr-2 noti-upload-icons" size="25" type="md-happy" />
                            <div class="emoji-area-popup">
                                <div v-if="emoStatus" class="position-absolute close-emoji-btn" @click="closeEmojiWindow()">{{$t('common').closeEmoji}}</div>
                                <Picker v-if="emoStatus" set="emojione" @select="onInput" title="Pick your emoji..." />
                            </div>
                            <Button icon="md-create" type="success" class="ml-auto" @click="updateCommunity" :disabled="isUpdating" :loading="isUpdating">Update Topic</Button>
                        </div>
                        <div class="col-12 uploaded_file">
                            <div class="image-item" v-if="updateCommunityData.upload_file.imgUrl && updateCommunityData.upload_file.imgUrl.length >0">
                                <div class="image-block">
                                    <div class="image-upload-list" v-for="(imgUrl,i) in updateCommunityData.upload_file.imgUrl" :key="i">
                                        <img :src="imgUrl" alt="">
                                        <div class="demo-upload-list-cover">
                                            <Icon  type="ios-trash-outline" @click="deleteFile('image',imgUrl)"></Icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="file-item row" v-if="updateCommunityData.upload_file.otherUrl.length &&updateCommunityData.upload_file.otherUrl.length>0">
                                <div class="col-6 col-md-4" v-for="(otherUrl,j) in updateCommunityData.upload_file.otherUrl" :key="j">
                                    <div class="image-upload-list float-left">
                                        <img src="/asset/img/icon/upload_file_img.png" class="w-100" alt="">
                                        <div class="demo-upload-list-cover">
                                            <Icon type="ios-trash-outline" @click="deleteFile('other',otherUrl)"></Icon>
                                        </div>
                                    </div>
                                    <div class="title pt-2">
                                        <div class="text-break">{{otherUrl.fileOriName}}</div>
                                        <div class="text-secondary">{{otherUrl.fileSize}}</div>
                                    </div>
                                    <div class="remark"></div>
                                </div>
                            </div>
                            <div class="file-item row" v-if="updateCommunityData.upload_file.videoUrl.length && updateCommunityData.upload_file.videoUrl.length>0">
                                <div class="col-6 col-md-4" v-for="(videoUrl,j) in updateCommunityData.upload_file.videoUrl" :key="j">
                                    <div class="image-upload-list float-left">
                                        <img src="/asset/img/icon/upload_video_img.png" class="w-100" alt="">
                                        <div class="demo-upload-list-cover">
                                            <Icon type="ios-trash-outline" @click="deleteFile('video',videoUrl)"></Icon>
                                        </div>
                                    </div>
                                    <div class="title pt-2">
                                        <div class="text-break">{{videoUrl.fileOriName}}</div>
                                        <div class="text-secondary">{{videoUrl.fileSize}}</div>
                                    </div>
                                </div>
                                <div class="remark"></div>
                            </div>
                        </div>
                    </div>
                </Form> 
            </div>
        </div>
    </div>
</template>

<script>
//
import wysiwyg from "vue-wysiwyg"
//emoji
import { Picker } from 'emoji-mart-vue'
//file upload component
import Upload from '~/components/Upload'
//import Api
import {updateCommunity} from '~/api/community'

import {delUploadFile} from '~/api/upload'
import { mapGetters } from 'vuex'
import i18n from '~/plugins/i18n'

export default {
    metaInfo () {
        return { title: this.$t('metaInfo').communityUpdate }
    },
    middleware: 'auth',

    components: {
        Picker,     //emoji
        Upload,
    },

    computed:{ 
        ...mapGetters({
            currentUser: 'auth/user'
        }),
        currentPath(){
            return this.$route
        },
    },

    props:{
        updateCommunityData : Object
    },

    mounted(){
    },

    data(){
        return{
            baseUrl:window.base_url,
            //emoji
            emoStatus:false,

            initPeriod:'forever',
            periodType:'withPeriod',
            isUpdating:false,

            //disable before time(TimePicker)
            disableBeforeDate: {
                disabledDate (date) {
                    return date && date.valueOf() < Date.now() - 86400000;
                }
            },
        }
    },

    created(){
        this.communityId = this.currentPath.params.id;
        // this.getCurrentCommunity();
    },

    methods:{
        closeEmojiWindow(){
            this.emoStatus = false;
        },
        
        toggleEmo(){
            this.emoStatus = !this.emoStatus;
        },
        onInput(e){
            if(!e){
                return false;
            }
            if(!this.updateCommunityData.content){
                this.updateCommunityData.content = e.native
            }else{
                this.updateCommunityData.content = this.updateCommunityData.content + e.native
            }
        },

        notiDateChange(date){
            this.updateCommunityData.period = date;
        },

        setPeriodToBlank(){
            this.updateCommunityData.period = '';
        },

        async updateCommunity(){
            if(this.updateCommunityData.title.trim() == ''){
                return this.error(i18n.t('alert').title);
            }
            if(this.periodType == 'withPeriod'){
                this.updateCommunityData.period = this.initPeriod;
                if(this.updateCommunityData.period.trim() == ''){
                    return this.error(i18n.t('alert').period);
                }
            }
            else{
                if(this.updateCommunityData.period.length == 0){
                    return this.error(i18n.t('alert').period);
                }
            }
            if(this.updateCommunityData.content.trim() == ''){
                return this.error(i18n.t('alert').desc);
            }
            this.isUpdating = true;

            await updateCommunity(this.updateCommunityData)
            .then(res=>{
                this.updateCommunityData.title = '';
                this.updateCommunityData.period = '';
                this.updateCommunityData.content = '';
                this.$router.push({path:'/community/index'})
            })
            .catch(err=>{
                console.log(err);
            })
            this.isUpdating = false;
        },

        async deleteFile(type, fileName){
            let filePath = '';
            if(type == 'image'){
                filePath = fileName
            }else {
                filePath = fileName.imgUrl
            }

            

            await delUploadFile(filePath)
            .then(res=>{
                    if(type == 'image'){
                        this.updateCommunityData.upload_file.imgUrl.pop(fileName)
                    }else if(type == 'other'){
                        this.updateCommunityData.upload_file.otherUrl.pop(fileName)
                    }else if(type == 'video'){
                        this.updateCommunityData.upload_file.videoUrl.pop(fileName)
                    }
                })
            .catch(err=>{
                console.log(err);
            })
        },

        //listen event from Upload
        upImgUrl(value) {
            this.updateCommunityData.upload_file.imgUrl.push(value);
        },
        upFileUrl(value) {
            this.updateCommunityData.upload_file.otherUrl.push(value);
        },
        upVideoUrl(value) {
            this.updateCommunityData.upload_file.videoUrl.push(value);
        },

        

    }
}
</script>