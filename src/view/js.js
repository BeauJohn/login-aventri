document.addEventListener('DOMContentLoaded', () => {
   const axios = require('axios')
   const form = comp.getElementsByClassName('s-login__form')[0]

   async function handleForm(e) {
      e.preventDefault()

      const mail = form.getElementsByClassName('s-login__mail')[0].value
      const referenceNumber = form.getElementsByClassName('s-login__number')[0]
         .value
      const msgDom = form.getElementsByClassName('s-msg')[0]
      const loginUrl = getLoginUrl()
      const body = {
         login: mail,
         ID: referenceNumber,
         projectName: PROJECTNAME
      }

      try {
         const response = await axios.post(loginUrl, body)
         const login = JSON.parse(response)

         if (login.status === 'success') {
            const { loginToken, userID, smartToken } = login
            sessionStorage.setItem('loginToken', loginToken)
            sessionStorage.setItem('userID', userID)

            const smartCUrl = getSmartContentUrl()

            try {
               const smartResponse = await axios.post(smartCUrl, {
                  smt: smartToken
               })

               const redirectUrl = form.dataset.redirect
               if (redirectUrl) window.location.replace(redirectUrl)
            } catch (error) {
               console.log(error)
            }
         } else if (login.status === 'failed') {
            msgDom.innerText = login.error
         }
      } catch (error) {
         console.log(error)
      } finally {
         form.reset()
      }
   }

   form.addEventListener('submit', handleForm)
})
