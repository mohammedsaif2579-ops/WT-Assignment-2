package com.campusly;

import java.util.ArrayList;
import java.util.List;

public class Student {
    private int id;
    private String name;
    private String rollNumber;
    private String email;
    private String course;
    private String phone;
    private final List<StudentFile> files = new ArrayList<>();

    public int getId() { return id; }
    public void setId(int id) { this.id = id; }
    public String getName() { return name; }
    public void setName(String name) { this.name = name; }
    public String getRollNumber() { return rollNumber; }
    public void setRollNumber(String rollNumber) { this.rollNumber = rollNumber; }
    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }
    public String getCourse() { return course; }
    public void setCourse(String course) { this.course = course; }
    public String getPhone() { return phone; }
    public void setPhone(String phone) { this.phone = phone; }
    public List<StudentFile> getFiles() { return files; }
}
