<template>
    <div class="container">
        <div class="row main">
            <div class="main-center">
                <h1>Upload Past Paper</h1>
                <form class="">

                    <div class="form-group">
                        <label for="name" class="control-label">File Name</label>
                        <div class="input-group">
                            <input type="text" v-model="name" class="form-control" name="name" id="name"
                                   placeholder="Enter File Name"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="resource" class="control-label">Past Paper Type</label>
                        <select class="form-control" v-model="resource_type" id="resource" autocomplete="off">
                            <option>Exam</option>
                            <option>CAT</option>
                            <option>Assignment</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="semester" class="control-label">Semester</label>
                        <div>
                            <div class="input-group">
                                <input type="text" class="form-control" v-model="semester" name="username"
                                       id="semester"
                                       placeholder="Enter Semester"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-file">
                            <input type="file" id="file" @change="onFileChange" class="file-input"
                                   accept="application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        </div>
                    </div>

                    <div class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                             aria-valuemax="100" style="width: 60%;">
                            60%
                        </div>
                    </div>

                    <div class="form-group ">
                        <a href="#" @click="uploadPaper" type="button" id="button"
                           class="btn btn-primary btn-lg btn-block">Upload</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import {uploadPastPaper} from '../file-upload.service';
    import axios from "axios";

    export default {
        name: 'app',
        data() {
            return {
                name: '',
                resource_type: '',
                semester: '',
                file: null
            }
        },
        methods: {
            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;

                this.file = files[0];
            },
            uploadPaper: function () {

                let formData = new FormData(this);
                formData.append("name", this.name);
                formData.append("resource_type", this.resource_type);
                formData.append("semester", this.semester);
                formData.append("file", this.file);

                uploadPastPaper(formData).then(x => {
                    console.log(x);
                }).catch(err => {
                    console.log(err);
                });
            }
        }
    }
</script>

<style lang="scss">
    body, html {
        font-family: 'Oxygen', sans-serif;
    }

    .main {
        margin: 50px 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    input {
        margin: auto;
        width: 100%;
    }

    #button {
        border: 1px solid #ccc;
        margin-top: 28px;
        padding: 6px 6px;
        cursor: pointer;
        -moz-border-radius: 3px 3px;
        -webkit-border-radius: 3px 3px;
        border-radius: 3px 3px;
        -moz-box-shadow: 0 1px #fff inset, 0 1px #ddd;
        -webkit-box-shadow: 0 1px #fff inset, 0 1px #ddd;
        box-shadow: 0 1px #fff inset, 0 1px #ddd;
    }

    .main-center {

        margin: 0 auto;
        max-width: 400px;
        padding: 10px 40px;
        text-shadow: none;
        -webkit-box-shadow: 0 3px 5px 0 rgba(0, 0, 0, 0.31);
        -moz-box-shadow: 0 3px 5px 0 rgba(0, 0, 0, 0.31);
        box-shadow: 0 3px 5px 0 rgba(0, 0, 0, 0.31);

    }
</style>
