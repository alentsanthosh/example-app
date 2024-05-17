<template>
  <div class="body-full">
    <div class="title">
      <h3 class="main">Multiple Image Upload</h3>
    </div>
    <el-form ref="form" :model="formData" :rules="formRules" label-width="100px">
      <el-form-item label="Name" prop="name">
        <el-input class="fname" v-model="formData.name"></el-input>
      </el-form-item>
      <el-form-item label="Images" prop="images">
        <el-upload
          class="upload-demo"
          :action="uploadUrl"
          :file-list="fileList"
          :before-upload="beforeUpload"
          :on-change="handleFileChange"
          multiple
          :auto-upload="false"
          ref="upload">
          <el-button size="small" type="primary">Click to upload</el-button>
        </el-upload>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" size="small" @click="submitForm" :disabled="isUploading">Submit</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      fileList: [],
      formData: {
        name: '',
        images: []
      },
      formRules: {
        name: [{ required: true, message: 'Please enter name', trigger: 'blur' }],
        images: [{ required: true, message: 'Please upload images', trigger: 'change' }]
      },
      uploadUrl: '/post-image',
      uploading: false
    };
  },
  computed: {
    isUploading() {
      return this.fileList.some(file => file.status === 'uploading');
    }
  },
  methods: {
    beforeUpload(file) {
      const isLt2M = file.size / 1024 / 1024 < 10;
      if (!isLt2M) {
        this.$message.error('The image size should be less than 10MB');
        return Promise.reject(new Error('The image size should be less than 10MB'));
      }

      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (e) => {
          const image = new Image();
          image.src = e.target.result;
          image.onload = () => {
            const isSquare = image.width === image.height;
            if (!isSquare) {
              this.$message.error('The image width and height should be same');
              reject(false);
            } else {
              resolve(true);
            }
          };
          image.onerror = () => {
            this.$message.error('Failed to load the image. Please try again.');
            reject(false);
          };
        };
        reader.onerror = () => {
          this.$message.error('Failed to read the image file. Please try again.');
          reject(false);
        };
      });
    },
    async handleFileChange(file, fileList) {
      const validFiles = [];
      for (let i = 0; i < fileList.length; i++) {
        try {
          const valid = await this.beforeUpload(fileList[i].raw);
          if (valid) {
            validFiles.push(fileList[i]);
          }
        } catch (error) {
          console.log(error);
        }
      }
      this.fileList = validFiles;
      this.formData.images = validFiles.map(item => item.raw);
    },
    async submitForm() {
      try {
        await this.$refs.form.validate();
        const formData = new FormData();
        formData.append('name', this.formData.name);
        this.formData.images.forEach((image, index) => {
          formData.append(`images[]`, image);
        });

        const response = await axios.post(this.uploadUrl, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        console.log(response.data);
        this.$refs.form.resetFields();
        this.fileList = [];
        this.formData.images = [];

        this.$message({
          message: 'Images uploaded successfully!',
          type: 'success'
        });

      } catch (error) {
        if (error.response) {
          console.error('Error response:', error.response);
        } else if (error.request) {
          console.error('Error request:', error.request);
        } else {
          console.error('Error message:', error.message);
        }
      }
    }
  }
};
</script>

<style scoped>
.title {
  background-color: rgb(59, 63, 63);
  width: 100%;
  height: 80px;
}

.main {
  position: absolute;
  right: 45%;
  top: 15px;
  color: white;
}

.body-full {
  margin-top: 0px;
}

.fname {
  width: 200px;
  padding-top: 10px;
}
</style>
