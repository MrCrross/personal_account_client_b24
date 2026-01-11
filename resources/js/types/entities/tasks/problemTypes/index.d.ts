export type ProblemType = {
    id: number,
    name: string,
    problems?: ProblemTypeProblem[] | null
}

export type ProblemTypeResponse = {
    data: ProblemType[]
}

export type ProblemTypeProblem = {
    id: number,
    name: string,
    problem_type_id?: number,
}
