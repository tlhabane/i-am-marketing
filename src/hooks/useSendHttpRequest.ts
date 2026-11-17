import { useCallback } from 'react';
import { BASE_URL } from '../config';

export type RequestMethod = 'POST' | 'GET';
export type RequestStatus = 'error' | 'pending' | 'sending' | 'successful';
export interface FormInput {
  value: any;
  error: string;
  required?: boolean;
  type: 'text' | 'email' | 'tel' | 'number' | 'password' | 'date';
  min?: any;
  max?: any;
}

export type FormData = Record<string, FormInput>;

type Props = {
    method?: RequestMethod;
    url: string;
    body?: Record<string, any>;
    headers?: Record<string, string>;
};

type SetErrors = (errors: Partial<Record<string, any>>) => void;

export const useSendHttpRequest = () => {
    return useCallback(async (httpRequestConfig: Props, setErrorHandler?: SetErrors) => {
      const { method = 'GET', headers = {}, body = {}, url } = httpRequestConfig;
      const requestUrl = `${BASE_URL}${url}`;
      const csrfToken = (window as any).csrfToken;

      const response = await fetch(requestUrl, {
        method,
        headers: {
          'Content-Type': 'application/json',
          ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
          ...headers,
        },
        body: method !== 'GET' ? JSON.stringify(body) : undefined,
        credentials: 'include',
      });

      // Update token if the server sends a fresh one in the response headers
      const newToken = response.headers.get('X-CSRF-TOKEN');
      if (newToken) {
        (window as any).csrfToken = newToken;
      }

      if (!response.ok) {
        const errorBody = await response.json().catch(() => ({} as Record<string, any>));
        if (
          Object.keys(errorBody?.fields || errorBody?.errors || {}).length > 0 &&
          setErrorHandler
        ) {
          setErrorHandler(errorBody?.fields || errorBody?.errors);
        }
        throw new Error(errorBody?.message || response.statusText);
      }

      const contentType = response.headers.get('content-type') || '';
      if (!contentType.includes('application/json')) {
        return {};
      }
      return (await response.json()) as Record<string, any>;
    }, []);
};
