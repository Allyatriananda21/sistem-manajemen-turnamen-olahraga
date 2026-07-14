import { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import API_BASE_URL from '../config/api';

export interface UseFetchResult<T> {
  data: T | null;
  loading: boolean;
  error: string | null;
  refetch: () => void;
}

export function useFetch<T>(endpoint: string, params: Record<string, any> = {}): UseFetchResult<T> {
  const [data, setData] = useState<T | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  // Serialize params to avoid infinite re-renders on object dependency checks
  const paramsString = JSON.stringify(params);

  const fetchData = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const url = endpoint.startsWith('http') ? endpoint : `${API_BASE_URL}${endpoint}`;
      const parsedParams = paramsString ? JSON.parse(paramsString) : {};
      
      const response = await axios.get(url, {
        params: parsedParams,
      });
      
      // Standard response mapping: response.data or response.data.data
      setData(response.data);
    } catch (err: any) {
      console.error('Error fetching data from:', endpoint, err);
      setError(
        err.response?.data?.message || 
        err.message || 
        'Gagal memuat data. Silakan coba lagi.'
      );
    } finally {
      setLoading(false);
    }
  }, [endpoint, paramsString]);

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  return { data, loading, error, refetch: fetchData };
}

export default useFetch;
