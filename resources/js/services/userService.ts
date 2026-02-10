import  api  from '../api'; // Ensure this path points to your axios instance file

/**
 * Fetch billing details
 */
export const fetchUser = () => 
    api.get(`/user/me`).then(res => res.data);