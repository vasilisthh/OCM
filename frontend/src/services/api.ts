import axios from 'axios';

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

export const leadService = {
  /**
   * Get all leads
   * @returns Promise with leads data
   */
  getLeads() {
    return apiClient.get('/leads');
  },
  
  /**
   * Get a single lead by ID
   * @param id Lead ID
   * @returns Promise with lead data
   */
  getLead(id: string | object) {
    let idToUse: string;
    
    if (!id) {
      return Promise.reject(new Error('Lead ID is required'));
    }
    
    if (typeof id === 'object' && id !== null) {
      if ('toString' in id && typeof id.toString === 'function') {
        idToUse = id.toString();
      } else if ('$oid' in id && typeof (id as any).$oid === 'string') {
        idToUse = (id as any).$oid;
      } else {
        return Promise.reject(new Error('Invalid ID format'));
      }
    } else {
      idToUse = String(id);
    }
    
    return apiClient.get(`/leads/${idToUse}`);
  },
  
  /**
   * Create a new lead
   * @param leadData Lead data object
   * @returns Promise with created lead data
   */
  createLead(lead: Record<string, any>) {
    return apiClient.post('/leads', lead);
  },
  
  /**
   * Update an existing lead
   * @param id Lead ID
   * @param leadData Updated lead data
   * @returns Promise with updated lead data
   */
  updateLead(id: string | object, lead: Record<string, any>) {
    let idToUse: string;
    
    if (!id) {
      return Promise.reject(new Error('Lead ID is required'));
    }
    
    if (typeof id === 'object' && id !== null) {
      if ('toString' in id && typeof id.toString === 'function') {
        idToUse = id.toString();
      } else if ('$oid' in id && typeof (id as any).$oid === 'string') {
        idToUse = (id as any).$oid;
      } else {
        return Promise.reject(new Error('Invalid ID format'));
      }
    } else {
      idToUse = String(id);
    }
    
    return apiClient.put(`/leads/${idToUse}`, lead);
  },
  
  /**
   * Delete a lead
   * @param id Lead ID
   * @returns Promise with deletion result
   */
  deleteLead(id: string | object) {
    let idToUse: string;
    
    if (!id) {
      return Promise.reject(new Error('Lead ID is required for deletion'));
    }
    
    if (typeof id === 'object' && id !== null) {
      if ('toString' in id && typeof id.toString === 'function') {
        idToUse = id.toString();
      } else if ('$oid' in id && typeof (id as any).$oid === 'string') {
        idToUse = (id as any).$oid;
      } else {
        return Promise.reject(new Error('Invalid ID format'));
      }
    } else {
      idToUse = String(id);
    }
    
    return apiClient.delete(`/leads/${idToUse}`);
  }
};

export default apiClient; 