import moment from "moment";
import axios from 'axios';
import { Message } from 'view-design';

let util = {};

util.title = function (title) {
    title = title ? title + ' - 调查问卷分析系统' : '调查问卷分析系统';
    window.document.title = title;
};

export default util;
