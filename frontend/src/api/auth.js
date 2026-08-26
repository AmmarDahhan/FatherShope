import axios from 'axios'
import api from './axios'

export const getCsrfCookie = () => axios.get('/sanctum/csrf-cookie', { withCredentials: true })
export const login = (credentials) => api.post('/auth/login', credentials)
export const logout = () => api.post('/auth/logout')
export const getMe = () => api.get('/auth/me')
