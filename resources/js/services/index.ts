import authController from './api/auth'
import bGroupsController from './api/bitrix/groups'
import bCompanyController from './api/bitrix/companies'
import usersController from './api/users'
import rolesController from './api/roles'
import problemTypesController from "./api/tasks/problemTypes";
import taskStatusesController from "./api/tasks/statuses";
import taskPrioritiesController from "./api/tasks/priorities";
import tasksController from "./api/tasks";

export const API = {
    auth: authController,
    b24: {
        groups: bGroupsController,
        companies: bCompanyController,
    },
    roles: rolesController,
    tasks: {
        problemTypes: problemTypesController,
        statuses: taskStatusesController,
        priorities: taskPrioritiesController,
        tasks: tasksController,
    },
    users: usersController,
}
