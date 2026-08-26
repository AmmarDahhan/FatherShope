import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

let isRedirecting = false

api.interceptors.response.use(
  (response) => response,
  (error) => {
    const url = error.config?.url || ''
    const status = error.response?.status

    if ((status === 401 || status === 419) && !url.includes('/auth/me') && !isRedirecting) {
      isRedirecting = true
      window.location.href = '/login'
    }

    return Promise.reject(error)
  }
)

export default api
