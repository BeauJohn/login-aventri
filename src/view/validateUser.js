document.addEventListener('DOMContentLoaded', () => {
   const axios = require('axios')
   const intervalTime = 3000
   let interval

   async function validateLogin() {
      const token = sessionStorage.getItem('loginToken')
      const userID = sessionStorage.getItem('userID')
      const validateTokenUrl = getValidateTokenUrl()
      const body = {
         userID: userID,
         token: token
      }

      try {
         const response = await axios.post(validateTokenUrl, body)
         const validToken = JSON.parse(response)

         if (!validToken) {
            //logout;
            window.location.href = './index.html'
            clearInterval(interval)
         }
      } catch (error) {
         console.log(error)
      }
   }

   interval = setInterval(validateLogin, intervalTime)
})
