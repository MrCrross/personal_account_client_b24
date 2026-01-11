import { AuthUser } from "@/types";

export type JwtToken = {
    user: AuthUser,
    access_token: string,
    token_type: string,
    expires_on: number
}

export type LoginFormInput = {
    email: string,
    password: string,
    remember: boolean,
}
